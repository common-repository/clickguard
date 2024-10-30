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

<div style="text-align: center">
    <h3>
        Ad Clicks – <strong><?php echo $config['date_ranges'][$date_range]; ?></strong>
    </h3>
    <div class="activity-block">
        <table id="clickguard-click-counts" cellspacing="0" align="center">
            <thead>
            <tr>
                <th style="background: #66BB6A; border: 1px solid #66BB6A">Total Ad<br />Clicks</th>
                <th style="background-color: #EF5350; border: 1px solid #EF5350">Flagged<br />Clicks</th>
                <th style="background-color: #FF7043; border: 1px solid #FF7043">Suspicious<br />Clicks</th>                
                <th style="background-color: #777; border: 1px solid #777">Invalid<br />Clicks</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="border: 1px solid #66BB6A"><?php echo $data['adClicksCount']; ?></td>
                <td style="border: 1px solid #EF5350"><?php echo $data['fraudulentClicksCount']; ?> <sub style="color: #EF5350">(<?php echo $data['fraudulentClicksShare']; ?>%)</sub></td>
                <td style="border: 1px solid #FF7043"><?php echo $data['suspiciousClicksCount']; ?> <sub style="color: #FF7043">(<?php echo $data['suspiciousClicksShare']; ?>%)</sub></td>                
                <td style="border: 1px solid #777"><?php echo $data['invalidClicksCount']; ?> <sub style="color: #777">(<?php echo $data['invalidClicksShare']; ?>%)</sub></td>
            </tr>
            </tbody>
        </table>
        <br class="clear" />
    </div>
    <ul class="subsubsub">
        <li>
            <a href="<?php echo admin_url('options-general.php?page=clickguard') ?>">Configure Plugin</a> |
        </li>
        <li>
            <a href="<?php echo $config['urls']['clickguard_dashboard']; ?>?account=<?php echo $account_id; ?>" target="_blank">
                ClickGUARD Dashboard <span aria-hidden="true" class="dashicons dashicons-external"></span>
            </a>
        </li>        
    </ul>
</div>
<br class="clear" />