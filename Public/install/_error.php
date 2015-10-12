<?php defined('PHALAPI_INSTALL') || die('no access'); ?>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_header.php'; ?>

      <div class="row">
        <h1>错误提示</h1>

        <br />

        <p><div role="alert" class="alert alert-danger alert-dismissible fade in">
        <?php echo $error; ?></div></p>

        <br />

        <p><a class="btn btn btn-success" href="#" role="button">我知道了</a></p>

      </div>

<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_footer.php'; ?>
