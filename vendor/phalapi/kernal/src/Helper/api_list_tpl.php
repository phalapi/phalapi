<?php
$env && ob_start ();
$table_color_arr = explode(" ", "red orange yellow olive teal blue violet purple pink grey black");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $projectName; ?> - 在线接口列表</title>
    <link href="https://cdn.bootcss.com/semantic-ui/2.2.2/semantic.min.css" rel="stylesheet">
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />

    <script src="https://cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/semantic-ui/2.2.2/semantic.min.js"></script>
    <meta name="robots" content="none"/>
</head>
<body>

  <div class="ui fixed inverted menu">
    <div class="ui container">
      <a href="/docs.php" class="header item">
        <img class="logo" src="http://7xiz2f.com1.z0.glb.clouddn.com/20180316214150_f6f390e686d0397f1f1d6a66320864d6">
        <?php echo $projectName; ?>
      </a>
      <a href="https://www.phalapi.net/" class="item">PhalApi</a>
      <a href="http://docs.phalapi.net/#/v2.0/" class="item">文档</a>
      <a href="http://qa.phalapi.net/" class="item">社区</a>
    </div>
  </div>

<div class="row"></div>
<br />
<br/>


<div class="ui text container" style="max-width: none !important; width: 1200px" id="menu_top">
    <div class="ui floating message">
        <?php
        if (!empty($errorMessage)) {
        echo  '<div class="ui error message">
            <strong>错误：' . $errorMessage . '</strong> 
            </div>';
        }
        ?>

        <div class="ui grid container" style="max-width: none !important;">
            <?php
            if ($theme == 'fold') {
            ?>
            <div class="four wide column">
                <div class="ui vertical accordion menu">
                <?php 
                    // 总接口数量
                    $methodTotal = 0;
                    foreach ($allApiS as $namespace => $subAllApiS) { 
                        foreach ($subAllApiS as $item) {
                            $methodTotal += count($item['methods']);
                        }
                    }
                ?>
                    <div class="item"><h4>接口服务列表&nbsp;(<?php echo $methodTotal; ?>)</h4></div>

                <?php 
                    $num = 0;
                    foreach ($allApiS as $namespace => $subAllApiS) {
                        echo '<div class="item">';
                        $subMethodTotal = 0;
                        foreach ($subAllApiS as $item) {
                            $subMethodTotal += count($item['methods']);
                        }
                        echo sprintf('<h4 class="title active" style="font-size:16px;margin:0px;"><i class="dropdown icon"></i>%s (%d)</h4>', $namespace, $subMethodTotal);
                        echo sprintf('<div class="content %s" style="margin-left:-16px;margin-right:-16px;margin-bottom:-13px;">', $num == 0 ? 'active' : '');
                        // 每个命名空间下的接口类
                        foreach ($subAllApiS as $key => $item) {
                            echo sprintf('<a class="item %s" data-tab="%s">%s</a>', $num == 0 ? 'active' : '', str_replace('\\', '_', $namespace) . $key, $item['title']);
                            $num++;
                        }
                        echo '</div></div><!-- END OF NAMESPACE -->';
                    } // 每个命名空间下的接口

                    ?>
                    <div class="item">
                        <div class="content" style="margin:-13px -16px;">
                            <a class="item" href="#menu_top">返回顶部↑↑↑</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?> <!-- 折叠时的菜单 -->

            <!-- 折叠时与展开时的布局差异 -->
            <?php if ($theme == 'fold') { ?>
            <div class="twelve wide stretched column">
            <?php } else { ?>
            <div class="wide stretched column">
            <?php 
                    // 展开时，将全部的接口服务，转到第一组
                    $mergeAllApiS = array('all' => array('methods' => array()));
                    foreach ($allApiS as $namespace => $subAllApiS) {
                        foreach ($subAllApiS as $key => $item) {
                            if (!isset($item['methods']) || !is_array($item['methods'])) {
                                continue;
                            }
                            foreach ($item['methods'] as $mKey => $mItem) {
                                $mergeAllApiS['all']['methods'][$mKey] = $mItem;
                            }
                        }
                    }
                    $allApiS = array('ALL' => $mergeAllApiS);
            } 
            ?>
                <?php
                $uri  = !$env ? substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')) : '';
                $num2 = 0;
                foreach ($allApiS as $namespace => $subAllApiS) {
                foreach ($subAllApiS as $key => $item) {
                    ?>
                    <div class="ui  tab <?php if ($num2 == 0) { ?>active<?php } ?>" data-tab="<?php echo str_replace('\\', '_', $namespace) . $key; ?>">
                        <table
                            class="ui red celled striped table <?php echo $table_color_arr[$num2 % count($table_color_arr)]; ?> celled striped table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>接口服务</th>
                                <th>接口名称</th>
                                <th>更多说明</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            $num = 1;
                            foreach ($item['methods'] as $mKey => $mItem) {
                                if ($env){
                                    ob_start ();
                                    // $_REQUEST['service'] = $mItem['service'];
                                    // $_GET['detail'] = 1;
                                    // include($webRoot . D_S . 'docs.php');

                                    // 换一种更优雅的方式
                                    \PhalApi\DI()->request = new \PhalApi\Request(array('service' => $mItem['service']));
                                    $apiDesc = new \PhalApi\Helper\ApiDesc($projectName);
                                    $apiDesc->render();

                                    $string = ob_get_clean ();
                                    \PhalApi\Helper\saveHtml ($webRoot, $mItem['service'], $string);
                                    $link = $mItem['service'] . '.html';
                                }else{
                                    $concator = strpos($uri, '?') ? '&' : '?';
                                    $link = $uri . $concator . 'service=' . $mItem['service'] . '&detail=1' . '&type=' . $theme;
                                }
                                $NO   = $num++;
                                echo "<tr><td>{$NO}</td><td><a href=\"$link\" target='_blank'>{$mItem['service']}</a></td><td>{$mItem['title']}</td><td>{$mItem['desc']}</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>

                    <!-- 主题切换，仅当在线时才支持 -->
                    <?php
                            if (!$env) {
                                $curUrl = $_SERVER['SCRIPT_NAME'];
                                if ($theme == 'fold') {
                                    echo '<div style="float: right"><a href="' . $curUrl . '?type=expand">切换回展开版</a></div>';
                                } else {
                                    echo '<div style="float: right"><a href="' . $curUrl . '?type=fold">切换回折叠版</a></div>';
                                }
                            }
                    ?>

                    </div>
                    <?php
                    $num2++;
                } // 单个命名空间的循环
                } // 遍历全部命名空间
                ?>


            </div>
        </div>
        <div class="ui blue message">
            <strong>温馨提示：</strong> 此接口服务列表根据后台代码自动生成，可在接口类的文件注释的第一行修改左侧菜单标题。
        </div>
        <p>&copy; Powered  By <a href="http://www.phalapi.net/" target="_blank">PhalApi <?php echo PHALAPI_VERSION; ?></a> <span id="version_update"></span> <p>
    </div>
    </div>
</div>
<script type="text/javascript">
    $('.accordion.menu a.item').tab({'deactivate':'all'});
    $('.ui.sticky').sticky();
	//当点击跳转链接后，回到页面顶部位置
    $(".accordion.menu a.item").click(function() {
        $('body,html').animate({
                scrollTop: 0
            },
            500);
        return false;
    });

    $('.ui.accordion').accordion({'exclusive':false});

    checkLastestVersion();

    // 检测最新版本
    function checkLastestVersion() {
        var version = '<?php echo PHALAPI_VERSION; ?>';
        $.ajax({
            url:'https://www.phalapi.net/check_lastest_version.php',
                type:'get',
                data:{version : version},
                success:function(res,status,xhr){
                    console.log(res);
                    if (!res.ret || res.ret != 200) {
                        return;
                    }
                    if (res.data.need_upgrade >= 0) {
                        return;
                    }          

                    $('#version_update').html('&nbsp; | &nbsp; <a target="_blank" href=" ' + res.data.url + ' "><strong>免费升级到 PhalApi ' + res.data.version + '</strong></a>');              
                },
                error:function(error){
                    console.log(error)
                }
        })

    }
</script>

</body>
</html>
<?php
if ($env){
    $string = ob_get_clean ();
    \PhalApi\Helper\saveHtml ($webRoot, 'index', $string);
    $str = "
Usage:

生成展开版：  php {$argv[0]} expand
生成折叠版：  php {$argv[0]} fold

脚本执行完毕！离线文档保存路径为：";
    if (strtoupper ( substr ( PHP_OS, 0,3)) == 'WIN'){
        $str = iconv ( 'utf-8', 'gbk', $str);
    }
    $str .= $webRoot . D_S . 'docs' ;
    echo $str, PHP_EOL, PHP_EOL;
    exit(0);
}

