<?php

// =============================================================================================
// ClickGUARD
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

?>

<div id="cg-wrap" class="wrap">

    <h2 id="cg-header">
        <img src="<?php echo plugin_dir_url(CG_ROOT) ?>/assets/logo-new.png" alt="ClickGUARD"/>
    </h2>

    <div id="cg-body">

        <table class="widefat cg-section">
            <tbody>
            <tr>
                <td>
                    <p>
                        Don't have a ClickGUARD account yet?
                        Sign up for free <a href="<?php echo $config['urls']['sign_up']; ?>" target="_blank">here</a>.
                    </p>

                    <table class="form-table">
                        <tbody>
                        <!-- API Key-->
                        <tr>
                            <th scope="row">API Key</th>
                            <td>
                                <input type="text" name="cg_api_key" id="cg_api_key" style="width: 400px" value="<?php echo $api_key; ?>" placeholder="Your ClickGUARD API key">
                                <div class="description">
                                    <p>
                                        <a class="cg-helper-button" href="#cg-helper-api-key">Where do I find my API key?</a>
                                    </p>
                                    <div id="cg-helper-api-key" class="cg-helper">
                                        <p>
                                            <a href="http://app.clickguard.com" target="_blank">Login</a> to your <strong>ClickGUARD</strong>
                                            account, click on the user icon in the top right corner of the screen and select "API Access":
                                        </p>
                                        <p><img src="<?php echo plugin_dir_url(CG_ROOT) ?>/assets/api_key.png" style="width: 250px" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Account ID-->
                        <tr>
                            <th scope="row">Account ID:</th>
                            <td>
                                <input type="email" name="cg_account_id" id="cg_account_id" style="width: 400px" value="<?php echo $account_id; ?>" placeholder="Your account ID in ClickGUARD">
                                <div class="description">
                                    <p>
                                        <a class="cg-helper-button" href="#cg-helper-account-id">Where do I find my account ID?</a>
                                    </p>
                                    <div id="cg-helper-account-id" class="cg-helper">
                                        <p>
                                            <a href="<?php echo $config['urls']['clickguard_login']; ?>" target="_blank">Login</a> to your <strong>ClickGUARD</strong>
                                            account, visit your <a href="<?php echo $config['urls']['clickguard_accounts']; ?>" target="_blank">Accounts</a> page
                                            and copy the account ID of the Google Ads account you wish to link with:
                                        </p>
                                        <p><img src="<?php echo plugin_dir_url(CG_ROOT) ?>/assets/account_id.png" style="width: 350px" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Tracking Code -->
                        <tr>
                            <th scope="row">Inject Tracking Code:</th>
                            <td>
                                <fieldset>
                                    <label for="blog_public">
                                        <input name="cg_set_tracking_code" type="checkbox" id="cg_set_tracking_code" value="1" <?php if($set_tracking_code > 0) echo 'checked="checked"'; ?>>
                                        Auto-set ClickGUARD tracking code
                                    </label>
                                    <p class="description">
                                        If selected the plugin will automatically inject ClickGUARD's tracking code <strong>on all pages</strong>.
                                    </p>
                                </fieldset>
                            </td>
                        </tr>
                        <!-- Dashboard Clicks -->
                        <tr>
                            <th scope="row">Dashboard Clicks:</th>
                            <td>
                                <select name="cg_date_range" id="cg_date_range" style="width: 400px">
                                    <?php foreach($config['date_ranges'] as $value => $label) { ?>
                                        <option value="<?php echo $value ?>" <?php if($value == $date_range) echo 'selected="selected";' ?>><?php echo $label ?></option>
                                    <?php } ?>
                                </select>
                                <p class="description">
                                    Display clicks summary for the given time period on the WordPress dashboard.
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <!-- Save -->
                    <p class="submit">
                        <?php
                            $buttonValue = 'Save';
                            switch($status) {
                                case CG_STATUS_INSTALLED: $buttonValue = 'Activate'; break;
                                case CG_STATUS_ACTIVE: $buttonValue = 'Update'; break;
                                case CG_STATUS_INACTIVE: $buttonValue = 'Reactivate'; break;
                            }
                        ?>
                        <input type="submit" name="cg_submit" id="cg_save_button" class="button button-primary" value="<?php echo $buttonValue; ?>">
                    </p>
                </td>
            </tr>
            </tbody>
        </table>

    </div>

</div>
<p style="text-align: center;">
    Plugin Version <strong><?php echo CG_VERSION; ?></strong> | &copy;
    <a href="https://clickguard.com" target="_blank">
        https://clickguard.com
    </a>
</p>
<div style="text-align: center">
    <table cellpadding="0" cellspacing="0" style="vertical-align: -webkit-baseline-middle; font-size: medium; font-family: Arial; display: inline-block;">
        <tbody>
        <tr style="text-align: right;">
            <td><a href="<?php echo $config['links']['facebook'] ?>" color="#2684d6" class="sc-hzDkRC kpsoyz" style="display: inline-block; padding: 6px; background-color: rgb(38, 132, 214); border-radius: 50%"><img src="<?php echo plugin_dir_url(CG_ROOT) ?>/assets/social/facebook.png" alt="facebook" color="#2684d6" height="18" class="sc-bRBYWo ccSRck" style="max-width: 135px; display: block;"></a></td>
            <td width="5"><div></div></td>
            <td><a href="<?php echo $config['links']['twitter'] ?>" color="#2684d6" class="sc-hzDkRC kpsoyz" style="display: inline-block; padding: 6px; background-color: rgb(38, 132, 214); border-radius: 50%"><img src="<?php echo plugin_dir_url(CG_ROOT) ?>/assets/social/twitter.png" alt="facebook" color="#2684d6" height="18" class="sc-bRBYWo ccSRck" style="max-width: 135px; display: block;"></a></td>
            <td width="5"><div></div></td>
            <td><a href="<?php echo $config['links']['linkedin'] ?>" color="#2684d6" class="sc-hzDkRC kpsoyz" style="display: inline-block; padding: 6px; background-color: rgb(38, 132, 214); border-radius: 50%"><img src="<?php echo plugin_dir_url(CG_ROOT) ?>/assets/social/linkedin.png" alt="facebook" color="#2684d6" height="18" class="sc-bRBYWo ccSRck" style="max-width: 135px; display: block;"></a></td>
            <td width="5"><div></div></td>
            <td><a href="<?php echo $config['links']['instagram'] ?>" color="#2684d6" class="sc-hzDkRC kpsoyz" style="display: inline-block; padding: 6px; background-color: rgb(38, 132, 214); border-radius: 50%"><img src="<?php echo plugin_dir_url(CG_ROOT) ?>/assets/social/instagram-sketched.png" alt="facebook" color="#2684d6" height="18" class="sc-bRBYWo ccSRck" style="max-width: 135px; display: block;"></a></td>
            <td width="5"><div></div></td>
            <td><a href="<?php echo $config['links']['youtube'] ?>" color="#2684d6" class="sc-hzDkRC kpsoyz" style="display: inline-block; padding: 6px; background-color: rgb(38, 132, 214); border-radius: 50%"><img src="<?php echo plugin_dir_url(CG_ROOT) ?>/assets/social/youtube.png" alt="facebook" color="#2684d6" height="18" class="sc-bRBYWo ccSRck" style="max-width: 135px; display: block;"></a></td>
        </tr>
        </tbody>
    </table>
</div>
<div class="clear"></div>

<script>
    
    jQuery(function(){

        let cg_error = function(title,message) {
            swal({
                title: title,
                text: message,
                confirmButtonText: 'Ok',
                type:'error'
            });
        };

        // helper buttons
        jQuery('.cg-helper-button').on('click', function(e){
            e.preventDefault();
            jQuery(jQuery(this).attr('href')).toggleClass('show');
            return false;
        });

        // submit
        jQuery('#cg_save_button').on('click',function(e){
            e.preventDefault();
            jQuery.LoadingOverlay('show');
            jQuery.ajax({
                type: "POST",
                dataType: 'json',
                url: '<?php echo admin_url('admin-ajax.php') ?>?action=clickguard',
                data: {
                    api_key: jQuery('#cg_api_key').val(),
                    account_id: jQuery('#cg_account_id').val(),
                    set_tracking_code: jQuery('#cg_set_tracking_code').is(':checked') ? 1 : 0,
                    date_range: jQuery('#cg_date_range').val()
                }
            }).done(function(response) {
                jQuery.LoadingOverlay('hide');
                if(response.error) {
                    cg_error('Error', response.error);
                    return;
                }
                swal({
                    title: 'Success!',
                    text: "You've successfully configured ClickGUARD for your site.",
                    confirmButtonText: 'OK',
                    type:'success',
                    onClose: function(){ window.location.reload(); }
                });
            }).fail(function(){
                jQuery.LoadingOverlay('hide');
                cg_error('Error', 'An unexpected error occurred.');
            });
        });

    });

</script>