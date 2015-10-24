<?php defined('PHALAPI_INSTALL') || die('no access'); ?>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_header.php'; ?>

<div class="radius bg bouncein window window_small">
    <div class="window_title t_success">
        <span class="icon-circle"> </span>
        <span class="icon-circle"></span>
        <span class="margin-small-left">安装完成</span>
    </div>
    <div class="padding-large text-black">
        <h1 class="margin-small-bottom" >恭喜您，已安装成功</h1>
        <h4 class="margin-big-bottom">接下来，是见证奇迹的时刻，框架的使用，请查看框架使用手册</h4>
        <hr>
        <div class="margin-big-top" >
            <a href="<?php echo $apiUrl; ?>" class="button bg-green">测试请求</a>
            <a href="http://www.phalapi.net/wikis/" class="button bg-yellow margin-left">查看开发文档</a>
            <a href="http://qa.phalapi.net/" class="button bg-blue margin-left">访问开发者社区</a>
        </div>
    </div>
</div>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_footer.php'; ?>
