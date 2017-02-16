<?php defined('PHALAPI_INSTALL') || die('no access'); ?>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_header.php'; ?>

<div class="radius bg bouncein window window_small">
    <div class="window_title t_success">
        <span class="icon-circle"> </span>
        <span class="icon-circle"></span>
        <span class="margin-small-left">Installation Wizard</span>
    </div>
    <div class="padding-large text-black">
        <h1 class="margin-small-bottom" >Congratulations! Project has been installed successfully.</h1>
        <h4 class="margin-big-bottom">What's next, it's time to do something great! And you can visit wikis on how to use PhalApi in short time.</h4>
        <hr>
        <div class="margin-big-top" >
            <a href="<?php echo $apiUrl; ?>" class="button bg-green">Test API request</a>
            <a href="http://www.phalapi.net/wikis/" class="button bg-yellow margin-left">Check Wikis</a>
            <a href="http://qa.phalapi.net/" class="button bg-blue margin-left">Goto Community</a>
        </div>
    </div>
</div>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_footer.php'; ?>
