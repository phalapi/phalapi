<?php defined('PHALAPI_INSTALL') || die('no access'); ?>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_header.php'; ?>
<div class="radius bg bouncein window window_small">
    <div class="window_title t_error">
        <span class="icon-circle"> </span>
        <span class="icon-circle"></span>
        <span class="margin-small-left">Error</span>
    </div>
    <div class="padding-large text-black">
        <h1 class="margin-small-bottom" >Something Wrong!</h1>
        <h4><?php echo $error ?></h4>
        <hr>
        <div class="margin-big-top" >
            <a class="button bg-yellow margin-small-right"  href="#" role="button">   I know  </a>
            <button class="button">Cancel</button>
        </div>
    </div>
</div>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_footer.php'; ?>
