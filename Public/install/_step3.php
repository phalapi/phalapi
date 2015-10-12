<?php defined('PHALAPI_INSTALL') || die('no access'); ?>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_header.php'; ?>

      <div class="row">
        <h1>接口请求</h1>
        <p class="lead">恭喜您，已安装成功(建议手动删除 ./install 此目录以及目录下的全部文件)！<br/>接下来，是见证奇迹的时刻</p>

        <br />

        <p><?php echo $apiUrl; ?></p>

        <br />

        <p><a class="btn btn btn-success" href="<?php echo $apiUrl; ?>" role="button">请求</a></p>

      </div>

<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_footer.php'; ?>
