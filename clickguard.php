<?php

// =============================================================================================
// ClickGUARD - Click Fraud Detection And Prevention
// https://clickguard.com
//
// Released under the GNU General Public Licence v2
// http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
//
// Please refer all questions/requests to: support@clickguard.com
//
// This is an add-on for WordPress
// http://wordpress.org/
// =============================================================================================

/*
    Plugin Name: ClickGUARD – Click Fraud Detection And Prevention
    Plugin URI: https://www.clickguard.com
    Description: Removes the pain and frustration of losing money to click fraud. Protects Google Ads campaigns from wasteful ad clicks.
    Version: 2.0
    Author: ClickGUARD
*/

// =============================================================================================
// Shared
// =============================================================================================

const CG_ROOT = __FILE__;
const CG_VERSION = '1.7';

const CG_STATUS_INSTALLED = 0;
const CG_STATUS_ACTIVE = 1;
const CG_STATUS_INACTIVE = -1;

const CG_PING_TIMEOUT = 2*60*60; // 2h

function clickguard_config()
{
    return array(
        //'api_url' => 'http://dashboard.local.clickguard.com/api/v2/',
        'api_url' => 'https://legacy.clickguard.com/api/v2/',
        'date_ranges' => array(
            'this_day' => 'Today',
            'this_month' => 'This Month',
        ),
        'urls' => array(
            'sign_up' => 'https://www.clickguard.com/?start-trial=yes',
            'clickguard_login' => 'http://legacy.clickguard.com/login',
            'clickguard_dashboard' => 'https://legacy.clickguard.com/dashboard/summary/',
            'clickguard_accounts' => 'http://legacy.clickguard.com/accounts',
            'facebook' => 'https://www.facebook.com/clickguard"',
            'twitter' => 'https://twitter.com/clickguard',
            'linkedin' => 'https://www.linkedin.com/company/clickguard/',
            'instagram' => 'https://www.instagram.com/clickguard/',
            'youtube' => 'https://www.youtube.com/clickguard',
        ),
    );
}

function clickguard_request($apiKey, $accountId, $method, $path, $data = array(), $ignoreCache = false)
{
    $headers = array('Content-Type' => 'application/json', 'Accept' => 'application/json');
    $cacheKey = 'cg_cache_' . md5($method . $path . json_encode($headers) . json_encode($data));

    if(!$ignoreCache) {
        $cache = get_option($cacheKey, null);
        if(null !== $cache) {
            $body = json_decode($cache, true);
            return array(200,$body);
        }
    }

    $config = clickguard_config();
    $path = $config['api_url'] . $apiKey . '/account/' . $accountId . '/wp/' . $path;
    $options = array('method' => $method, 'headers' => $headers);

    if($method == 'GET' && !empty($data)) {
        $get = array();
        if($ignoreCache) {
            $get['_'] = time();
        }
        foreach($data as $key => $value) {
            $get[] = $key . '=' . $value;
        }
        $path .= '?' . implode('&', $get);
    }

    switch ($method) {
        case 'GET':
            $response = wp_remote_get($path, $options);
            break;
        case 'POST':
        case 'PATCH':
            $data = json_encode($data);
            $headers['Content-Length'] = strlen($data);
            $options['body'] = $data;
            $response = wp_remote_post($path, $options);
            break;
        default:
            $response = wp_remote_request($path, $options);
    }

    if($response instanceof WP_Error) {
        return array(500,null);
    }

    $status = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    if($status == 200) {
        update_option($cacheKey, $body);
    }

    return array($status,json_decode($body, true));
}

function clickguard_clear_all_cache()
{
    global $wpdb;
    $wpdb->query("delete from {$wpdb->options} where option_name like 'cg_cache_%'");
}

// =============================================================================================
// Update Handler
// =============================================================================================

function clickguard_handle_update_to_2_0()
{
    if(get_option('cg_status') != CG_STATUS_ACTIVE) return false;
    $response = wp_remote_get('https://legacy.clickguard.com/api/v1/client/api-key', array('method' => 'GET', 'headers' => array(
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Access-Token' => get_option('cg_access_token', 'none')
    )));

    $status = wp_remote_retrieve_response_code($response);
    if($status != 200) return false;

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if($body == null || !isset($body['data']) || !isset($body['data']['api_key'])) return false;

    update_option('cg_api_key', $body['data']['api_key']);
    return true;
}

// =============================================================================================
// Admin
// =============================================================================================

function clickguard_ping()
{
    // ping every hour
    $lastPing = (int)get_option('cg_last_ping', 0);
    if($lastPing > 0 && time()-$lastPing <= CG_PING_TIMEOUT) {
        return true;
    }
    update_option('cg_last_ping', time());

    list($requestStatus,$body) = clickguard_request(get_option('cg_api_key','unknown'), get_option('cg_account_id','unknown'),'POST', 'tracking-code', array(), true);
    if($requestStatus == 500) {
        return true; // ignore server errors
    } elseif($requestStatus != 200) {
        //update_option('cg_status_text',"Unable to access your ClickGUARD account. Please register your site using your API key.");
        update_option('cg_status',CG_STATUS_INACTIVE);
        return false;
    }

    update_option('cg_tracking_code', $body['data']['tracking_code']);
    update_option('cg_account_name', $body['account_name']);
    return true;
}

add_action('admin_notices',function() {
    $version = get_option('cg_version', 0);
    if(CG_VERSION != $version) {
        if($version == 0) {
            ?>
            <div class="updated">
                <p><strong>ClickGUARD</strong> was successfully installed.</p>
                <p>Please <a href="<?php echo admin_url('options-general.php?page=clickguard'); ?>">register your site</a>.</p>
            </div>
            <?php
        } elseif($version == '2.0') {
            if(!clickguard_handle_update_to_2_0()) { ?>
                <div class="updated">
                    <p><strong>ClickGUARD</strong> updated to version 2.0.</p>
                    <p><strong>IMPORTANT</strong>:  <a href="<?php echo admin_url('options-general.php?page=clickguard'); ?>">plugin settings</a> to update your configuration and reactivate the plugin!</p>
                </div>
            <?php } else { ?>
                <div class="updated">
                    <p><strong>ClickGUARD</strong> updated to version 2.0.</p>
                    <p>Your plugin configuration was automatically adjusted. Visit <a href="<?php echo admin_url('options-general.php?page=clickguard'); ?>">plugin settings</a> to review.</p>
                </div>
            <?php }
        } else {
            ?>
            <div class="updated">
                <p><strong>ClickGUARD</strong> was successfully updated to version <?php echo CG_VERSION ?>.</p>
                <p>Check your <a href="<?php echo admin_url('options-general.php?page=clickguard'); ?>">settings</a>.</p>
            </div>
            <?php
        }
        add_option('cg_version',CG_VERSION);
        update_option('cg_version',CG_VERSION);
    }

    switch(get_option('cg_status')) {
        case CG_STATUS_ACTIVE:
            if(clickguard_ping()) {
                break;
            }
        case CG_STATUS_INACTIVE:
            ?>
            <div class="error">
                <p><strong>ClickGUARD</strong> plugin is not active!</p>
                <p class="cg-visit-settings">Visit <a href="<?php echo admin_url('options-general.php?page=clickguard'); ?>">plugin settings</a> to update your configuration.</p>
            </div>
            <?php
            break;
    }
});

add_filter('plugin_action_links_' . plugin_basename(CG_ROOT), function($links){
    return array_merge($links, array(
        '<a href="' . admin_url('options-general.php?page=clickguard') . '">Settings</a>',
    ));
});

add_action('admin_menu', function(){
    add_options_page(
        'ClickGUARD',
        '🛡 ClickGUARD',
        'manage_options',
        'clickguard',
        function(){
            if (!current_user_can('manage_options')) return;

            wp_enqueue_style('clickguard-admin', plugin_dir_url(CG_ROOT) . '/assets/admin.css', array(), CG_VERSION);
            wp_enqueue_script('clickguard-loverlay', plugin_dir_url(CG_ROOT) . '/lib/loading-overlay/loadingoverlay.min.js', array(), CG_VERSION);
            wp_enqueue_script('clickguard-swal', plugin_dir_url(CG_ROOT) . '/lib/sweetalert2.all.js', array(), CG_VERSION);

            $config = clickguard_config();
            $status = get_option('cg_status', CG_STATUS_INSTALLED);
            $api_key = get_option('cg_api_key', '');
            $account_id = get_option('cg_account_id', '');
            $account_name = get_option('cg_account_name', 'Unknown');
            $set_tracking_code = get_option('cg_set_tracking_code', 0);
            $date_range = get_option('cg_date_range');

            include plugin_dir_path(CG_ROOT) . '/tmpl/admin.php';
        }
    );
});

add_action('wp_ajax_clickguard', function(){
    header("Content-Type: application/json");
    $response = array();

    foreach(array('api_key','account_id') as $field) {
        if(!isset($_REQUEST[$field]) || empty($field)) {
            $response['error'] = 'You need to provide the API key and account ID.';
            echo json_encode($response);
            die;
        }
    }

    list($requestStatus,$body) = clickguard_request($_REQUEST['api_key'], $_REQUEST['account_id'],'POST', 'tracking-code', array(), true);
    if($requestStatus != 200) {
        $response['error'] = $body !== null ? $body['error'] : 'Unable to activate ClickGUARD, please check your input.';
        update_option('cg_status', CG_STATUS_INACTIVE);
        echo json_encode($response);
        die;
    }

    update_option('cg_api_key', $_REQUEST['api_key']);
    update_option('cg_account_id', $_REQUEST['account_id']);
    update_option('cg_account_name', $body['account_name']);
    update_option('cg_tracking_code', $body['data']['tracking_code']);
    update_option('cg_set_tracking_code', isset($_REQUEST['set_tracking_code']) ? $_REQUEST['set_tracking_code'] : 0);
    update_option('cg_date_range', isset($_REQUEST['date_range']) ? $_REQUEST['date_range'] : 'this_day');
    update_option('cg_status', CG_STATUS_ACTIVE);

    echo json_encode($response);
    die;
});

// =============================================================================================
// Activation, CRON
// =============================================================================================

register_activation_hook( __FILE__, function(){
    if(!wp_next_scheduled('clickguard_clean_cache')) {
        wp_schedule_event(time(), 'hourly', 'clickguard_clean_cache');
    }
});

register_deactivation_hook( __FILE__, function(){
    wp_clear_scheduled_hook('clickguard_clean_cache');
});

add_action('clickguard_clean_cache', 'clickguard_clear_all_cache');

// =============================================================================================
// Content
// =============================================================================================

add_action('wp_footer', function(){
    $status = get_option('cg_status', CG_STATUS_INSTALLED);

    if($status == CG_STATUS_ACTIVE) {
        if(get_option('cg_set_tracking_code',0) > 0) {
            echo get_option('cg_tracking_code', '');
        }
    }
});

add_action('wp_dashboard_setup', function(){
    if(get_option('cg_status') == CG_STATUS_ACTIVE) {
        $account_name = get_option('cg_account_name', 'Unknown');
        wp_add_dashboard_widget('clickguard_widget', '🛡 ClickGUARD – ' . $account_name, function(){
            $config = clickguard_config();
            $api_key = get_option('cg_api_key', 'unknown');
            $account_id = get_option('cg_account_id', 'unknown');
            $date_range = get_option('cg_date_range', 'this_day');

            $params = array();
            switch ($date_range) {
                case 'this_day':
                    $params['from'] = date('Y-m-d');
                    $params['to'] = $params['from'];
                    break;
                case 'this_month':
                    $params['from'] = date('Y-m') . '-01';
                    $params['to'] = date('Y-m-d');
                    break;
            }

            list($requestStatus,$body) = clickguard_request($api_key, $account_id,'GET', 'click-counts', $params);
            if($requestStatus != 200) {
                echo '<p>Error getting click summary counts</p>';
            } else {
                $data = $body['data'];
                wp_enqueue_style('clickguard-widget', plugin_dir_url(CG_ROOT) . '/assets/widget.css', array(), CG_VERSION);
                include plugin_dir_path(CG_ROOT) . '/tmpl/widget.php';
            }
        });
    }
});