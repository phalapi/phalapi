<!DOCTYPE html>
<html lang="en">
<!-- 

  _____   _             _                   _   _____             
 |  __ \ | |           | |    /\           (_) |  __ \            
 | |__) || |__    __ _ | |   /  \    _ __   _  | |__) |_ __  ___  
 |  ___/ | '_ \  / _` || |  / /\ \  | '_ \ | | |  ___/| '__|/ _ \ 
 | |     | | | || (_| || | / ____ \ | |_) || | | |    | |  | (_) |
 |_|     |_| |_| \__,_||_|/_/    \_\| .__/ |_| |_|    |_|   \___/ 
                                    | |                           
                                    |_|                           
 -->

<?php
$semanticPath = 'https://cdn.bootcss.com/semantic-ui/2.2.2/'; // cdn
$semanticPath = '/semantic/'; // 本地
?>

<head>
    <meta charset="utf-8">
    <title><?php echo $projectName; ?> - 在线接口文档</title>

    <link rel="stylesheet" href="<?php echo $semanticPath; ?>semantic.min.css">
    <link rel="stylesheet" href="<?php echo $semanticPath; ?>components/table.min.css">
    <link rel="stylesheet" href="<?php echo $semanticPath; ?>components/container.min.css">
    <link rel="stylesheet" href="<?php echo $semanticPath; ?>components/message.min.css">
    <link rel="stylesheet" href="<?php echo $semanticPath; ?>components/label.min.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />

    <script src="/static/jquery.min.js"></script>
    <script src="/static/jquery.cookie.min.js"></script>
    
        
    <style type="text/css">
    body {
      background-color: #DADADA;
    }
    body > .grid {
      height: 100%;
    }
    .image {
      margin-top: -100px;
    }
    .column {
      max-width: 450px;
    }
    

  </style>
</head>

<body>


<?php include dirname(__FILE__) . '/api_menu.php'; ?>

<!-- 
    <div class="ui text container" style="max-width: none !important;">
        <div class="ui floating message">
        <div class="ui  segment">
        <form method="post">
      <div class="ui form">
        <div class="two fields">
          <div class="field">
            <label>文档查看密码：</label>
            <input placeholder="查看当前文档，需要先输入查看密码" name="view_code" type="password">
          </div>
        </div>
        <button class="ui submit button blue">确定</button>
        </form>
      </div>
    </div>
   </div>
    -->

    
<div class="ui middle aligned center aligned grid">
  <div class="column">
    <h2 class="ui image header">
      <img src="/favicon.ico" alt="pro" class="image">
      <div class="content">
        <?php echo $projectName; ?>
      </div>
    </h2>
    <form class="ui large form" method="post" >
      <div class="ui stacked segment">
        <div class="field">
            <label>文档查看密码：</label>
          <div class="ui left icon input">
            <i class="lock icon"></i>
            <input type="password" name="view_code" placeholder="请输入查看密码">
          </div>
        </div>
        <button class="ui fluid large blue submit button">确定</button>
      </div>


    </form>

	<?php if (!empty($submitError)) { ?>
      <div class="ui error message"><?php echo $submitError; ?></div>
    <?php } ?>
  </div>
</div>

 </div>
        
<?php include dirname(__FILE__) . '/api_footer.php';?>

</body>
</html>
