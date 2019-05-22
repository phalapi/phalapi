<?php
$table_color_arr = explode(" ", "red orange yellow olive teal blue violet purple pink grey black");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $projectName; ?> - 在线接口列表</title>

    <!-- <link href="https://lib.baomitu.com/semantic-ui/2.3.3/semantic.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/semantic.min.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />

    <script src="https://libs.baidu.com/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://lib.baomitu.com/semantic-ui/2.3.3/semantic.min.js"></script>
    <meta name="robots" content="none"/>
</head>
<body>

  <div class="ui fixed inverted menu">
    <div class="ui container">
      <a href="/docs.php" class="header item">
        <img class="logo" src="http://cdn7.phalapi.net/20180316214150_f6f390e686d0397f1f1d6a66320864d6">
        <?php echo $projectName; ?>
      </a>
      <a href="https://www.phalapi.net/" class="item">PhalApi</a>
      <a href="http://docs.phalapi.net/#/v2.0/" class="item">文档</a>
      <a href="http://qa.phalapi.net/" class="item">社区</a>

     <div class="right menu">
         <div class="item">
             <div class="ui icon input">
             <form action="/docs.php?search=k" method="get">
                 <input type="text" name="keyword" placeholder="搜索接口" value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : ''; ?>">
             </form>
             </div>
         </div>
      </div>
    </div>
  </div>

<div class="row" style="margin-top: 60px;" ></div>

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

                                // 根据搜索关键字，匹配接口名称、功能说明、具体描述 - START
                                if (!empty($_GET['keyword'])) {
                                    $keyword = $_GET['keyword'];
                                    $isMatchByKeyword = false;
                                    if (stripos($mItem['service'], $keyword) !== false) {
                                        $isMatchByKeyword = true;
                                    } else if (stripos($mItem['title'], $keyword) !== false) {
                                        $isMatchByKeyword = true;
                                    } else if (stripos($mItem['desc'], $keyword) !== false) {
                                        $isMatchByKeyword = true;
                                    }
                                    // 未匹配，则跳过
                                    if (!$isMatchByKeyword) {
                                        continue;
                                    }
                                }
                                // 根据搜索关键字，匹配接口名称、功能说明、具体描述 - END

                                $mergeAllApiS['all']['methods'][$mKey] = $mItem;
                            }
                        }
                    }
                    $allApiS = array('ALL' => $mergeAllApiS);
            }
            ?>
                <?php
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
                                $link = $this->makeApiServiceLink($mItem['service'],$theme);
                                $NO   = $num++;
                                echo "<tr><td>{$NO}</td><td><a href=\"$link\" target='_blank'>{$mItem['service']}</a></td><td>{$mItem['title']}</td><td>{$mItem['desc']}</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>

                    <!-- 主题切换，仅当在线时才支持 -->
                    <?php
                    $this->makeThemeButton($theme);
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
    </div>
</div>

  <div class="ui inverted vertical footer segment" style="margin-top:30px; background: #1B1C1D none repeat scroll 0% 0%;" >
    <div class="ui container">
      <div class="ui stackable inverted divided equal height stackable grid">
        <div class="eight wide column centered">
            <div class="column" align="center" >
                <img src="https://www.phalapi.net/images/icon_logo.png" alt="PhalApi">
            </div>
            <div class="column" align="center">
                <p>
                    <strong>接口，从简单开始！</strong>
                    © 2015-<?php echo date('Y'); ?> Powered  By <a href="http://www.phalapi.net/" target="">PhalApi <?php echo PHALAPI_VERSION; ?> </a> All Rights Reserved. <span id="version_update"></span>
                </p>
            </div>
        </div>
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

