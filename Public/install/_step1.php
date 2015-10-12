<?php defined('PHALAPI_INSTALL') || die('no access'); ?>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_header.php'; ?>

      <div class="row">
        <h1>环境检查</h1>
        <p class="lead">为了确保接口顺利响应, 您的服务器需要满足以下系统需求的运行环境</p>

        <br />

        <table class="table table-bordered">
            <tbody>
                <?php $num = 0; ?>
                <?php foreach ($checkList as $item) { ?>
                <tr class="<?php if ($item['status'] == -1) echo 'alert alert-danger'; else if ($item['status'] == 1) echo 'alert alert-success';?>">
                    <th scope="row"><?php echo ++ $num; ?></th>
                    <th><?php echo $item['name']; ?></th>
                    <td><?php echo $item['tip']; ?></td>
                    <td><?php 
                            if ($item['status'] == 1) {
                               echo '<span aria-hidden="true" class="glyphicon glyphicon-ok"></span>';
                            } else {
                                echo '<span aria-hidden="true" class="glyphicon glyphicon-remove"></span>';
                            }
                    ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <br />

        <p><a class="btn btn btn-success" href="./?step=2" role="button">下一步</a></p>
      </div>

<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_footer.php'; ?>
