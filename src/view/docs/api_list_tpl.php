<?php
$table_color_arr = explode(" ", "red orange yellow olive teal blue violet purple pink grey black");
$semanticPath = './semantic/'; // 本地
if (substr(PHP_SAPI, 0, 3) == 'cli') {
    $semanticPath = 'https://cdn.bootcss.com/semantic-ui/2.2.2/';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $projectName; ?> - <?php echo \PhalApi\T('Online API Docs'); ?></title>

    <meta name="description" content="<?php echo $projectName; ?>。基于PhalApi开源接口框架。">
    <meta name="keywords" content="<?php echo $projectName; ?>,PhalApi">

    <!-- <link href="https://lib.baomitu.com/semantic-ui/2.3.3/semantic.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="<?php echo $semanticPath; ?>semantic.min.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />

    <script src="/static/jquery.min.js"></script>
<script src="<?php echo $semanticPath; ?>semantic.min.js"></script>
    <meta name="robots" content="none"/>
</head>
<body>


<?php include dirname(__FILE__) . '/api_menu.php';?>

<div class="row" style="margin-top: 60px;" ></div>

<div class="ui text container" style="max-width: none !important;" id="menu_top">
    <div class="ui floating message">
        <?php
        if (!empty($errorMessage)) {
        echo  '<div class="ui error message">
            <strong>' . $errorMessage . '</strong> 
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
                    <div class="item"><h4><?php echo \PhalApi\T('API List'); ?>&nbsp;(<?php echo $methodTotal; ?>)</h4></div>

                <?php
                    $num = 0;
                    foreach ($allApiS as $namespace => $subAllApiS) {
                        echo '<div class="item">';
                        $subMethodTotal = 0;
                        foreach ($subAllApiS as $item) {
                            $subMethodTotal += count($item['methods']);
                        }
                        $namespaceService = str_replace('\\', '_', trim($namespace, '\\'));
                        echo sprintf('<h4 class="title active" style="font-size:16px;margin:0px;"><i class="dropdown icon"></i>%s (%d)</h4>', \PhalApi\T($namespaceService), $subMethodTotal);
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
                            <a class="item" href="#menu_top">Top <i class="icon angle double up"></i></a>
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
                            class="ui red celled striped table celled striped table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo \PhalApi\T('API Service'); ?></th>
                                <th><?php echo \PhalApi\T('Request Method'); ?></th>
                                <th><?php echo \PhalApi\T('API Title'); ?></th>
                                <th><?php echo \PhalApi\T('API Description'); ?></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            $num = 1;
                            foreach ($item['methods'] as $mKey => $mItem) {
                                $s = str_replace('\\', '_', $mItem['service']);
                                $mItemMethods = !empty($mItem['methods']) ? $mItem['methods'] : 'GET/POST';
                                $mItemMethods = sprintf('<span class="ui label %s small">%s</span>', strtoupper($mItemMethods) == 'POST' ? 'green' : 'blue', $mItemMethods);
                                $link = $this->makeApiServiceLink($s,$theme);
                                $NO   = $num++;
                                echo "<tr><td>{$NO}</td><td>{$mItemMethods}</td><td><a href=\"$link\" target='_blank'>{$s}</a></td><td>{$mItem['title']}</td><td>{$mItem['desc']}</td></tr>";
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
            <strong><?php echo \PhalApi\T('Tips: '); ?></strong> <?php echo \PhalApi\T('This API Document will be generated automately by PHP code and comments.'); ?>
        </div>
    </div>
</div>


<?php include dirname(__FILE__) . '/api_footer.php';?>

<script type="text/javascript">

$(function(){
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
});
</script>

</body>
</html>
