<?php if (!defined('API_ROOT')) {
    exit;
} ?>
<?php \PhalApi\DI()->respose->load('header');?>

<h2>出错啦！</h2>

        错误码：<?php echo $this->ret; ?><br />
        错误详情：<?php echo $this->msg; ?>


