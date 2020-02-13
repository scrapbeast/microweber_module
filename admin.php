<?php
/**
 * Dev: Scrap Beast
 * Emai: info@scrapbeast.com
 * Date: 02/13/2020
 * Time: 20:30 AM
 */
only_admin_access();

if (isset($_POST['live_report_url'])) {

    $liveReportUrl = trim($_POST['live_report_url']);

    $optionData = array();
    $optionData['option_value'] = $liveReportUrl;
    $optionData['option_key'] = 'live_report_url';
    $optionData['option_group'] = 'scrapbeast';
    save_option($optionData);

    include 'src/ScrapBeastImport.php';

    $import = new ScrapBeastImport();
    $import->setSourceUrl($liveReportUrl);
    $import->start();

    $messages = '<div class="mw-ui-box mw-ui-box-content mw-ui-box-notification">Settings are saved.</div>';
}

?>

<?php if (isset($params['backend'])): ?>
    <module type="admin/modules/info"/>
<?php endif; ?>

<script>

</script>

<div id="mw-admin-content" class="admin-side-content">


    <div class="mw-ui-box">
        <div class="mw-ui-box-header">
            <span>Import Products</span>
        </div>
        <div class="mw-ui-box-content">

            <?php  if (isset($messages)):?>
                <?php echo $messages; ?>
                <br >
            <?php endif; ?>

            <form method="post">
            <div class="demobox">
                <label class="mw-ui-label">Scrap Beast Live Report Url </label>
                <input type="text" name="live_report_url" value="<?php echo get_option('live_report_url', 'scrapbeast'); ?>" class="mw-ui-field" style="width: 100%">
            </div>

            <br />

            <button type="submit" class="mw-ui-btn mw-ui-btn-info">Start Importing</button>
            </form>

        </div>
    </div>

</div>
