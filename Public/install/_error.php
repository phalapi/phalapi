<?php defined('PHALAPI_INSTALL') || die('no access'); ?>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_header.php'; ?>
<div class="radius bg bouncein window window_small">
    <div class="window_title t_error">
        <span class="icon-circle"> </span>
        <span class="icon-circle"></span>
        <span class="margin-small-left">错误提示</span>
    </div>
    <div class="padding-large text-black">
        <h1 class="margin-small-bottom" >安装遇到问题</h1>
        <h4><?php echo $error ?></h4>
        <hr>
        <div class="margin-big-top" >
            <a class="button bg-yellow margin-small-right"  href="#" role="button">   我知道了  </a>
            <button class="button">取消</button>
        </div>
    </div>
</div>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_footer.php'; ?>
