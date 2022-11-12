<?php
$semanticPath = './semantic/'; // 本地
if (substr(PHP_SAPI, 0, 3) == 'cli') {
    $semanticPath = 'https://cdn.bootcss.com/semantic-ui/2.2.2/';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $projectName; ?></title>

    <meta name="description" content="<?php echo $projectName; ?>。基于PhalApi开源接口框架。">
    <meta name="keywords" content="<?php echo $projectName; ?>,PhalApi">

    <link rel="stylesheet" href="<?php echo $semanticPath; ?>semantic.min.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />

    <script src="/static/jquery.min.js"></script>
<script src="<?php echo $semanticPath; ?>semantic.min.js"></script>
    <meta name="robots" content="none"/>

    <style type="text/css">
    </style>

</head>
<body>

<?php include dirname(__FILE__) . '/../docs/api_menu.php';?>

<div class="row" style="margin-top: 60px;" ></div>

<div class="ui alternate stripe vertical segment blue inverted" >
  <div class="ui stackable center aligned grid container">
    <div class="fourteen wide column">
      <h1 class="ui icon header inverted">
        <img class="ui inline icon image" src="/phalapi_logo.png" >
        <?php echo $projectName; ?>
      </h1>
      <div class="ui stackable center aligned vertically padded grid">
        <div class=" wide column">
          <p>PhalApi是一个PHP轻量级开源接口框架，致力于快速开发接口服务。支持HTTP/SOAP/RPC等协议，可用于搭建接口/微服务/RESTful接口/Web Services。</p>
          <p>接口从简单开始！</p>
          <a class="ui large right labeled green icon button" href="/docs.php">
            <i class="right chevron icon"></i>
            查看接口文档
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="feature alternate ui stripe vertical segment">
  <div class="ui three column center aligned divided relaxed stackable grid container">
    <div class="row">
      <div class="column">
        <h2 class="ui icon header">
          <img class="ui icon image" src="/semantic/rocket.png" style="height: 65px;">
          更简单的API接口开发
        </h2>
        <p>立即编写和运行你的第一个API接口，通过参数：<strong><code>s=接口服务名称</code></strong> 可以指定调用的API接口。</p>
        <a class="ui large button" href="/?s=App.Hello.World" target="_blank">Hello World</a>
      </div>
      <div class="column">
        <h2 class="ui icon header">
          <img class="ui icon image" src="/semantic/book.png" style="height: 65px;">
          PhalApi 2.x 完整开发文档
        </h2>
        <p>PhalApi，简称π框架，是一个PHP轻量级开源接口框架，专注于服务端接口开发。</p>
        <a class="ui primary view-ui large button" href="http://docs.phalapi.net/#/v2.0/tutorial" target="_blank">查看开发文档</a>
      </div>
      <div class="column">
        <h2 class="ui icon header">
          <img class="ui icon image" src="/semantic/lab.png" style="height: 65px;">
          PhalApi.net 官网
        </h2>
        <p>
          拥有自动生成的在线接口文档、多种开发语言的客户端SDK包以及可重用的扩展类库。
        </p>
        <a href="https://www.phalapi.net/" class="ui large button" target="_blank">发现更多</a>
      </div>
    </div>
  </div>
</div>

<div class="ui vertical stripe intro " style="margin-top:30px;">
  <div class="ui stackable very relaxed center aligned grid container">
    <div class="row">
      <div class="twelve wide column">
        <h1 class="ui header">为研发赋新能，接口从简单开始！</h1>
        <p >
基于PhalApi开发的，
项目 (6000+)，API数量（8W+），每日接口请求（10KW+）。

PhalApi作为可能是国内领先的PHP接口开发框架，已经应用在：云服务、亲子教育、共享出行、新鲜购、生活圈等领域。
为开发者所喜欢，为企业所认可。
</p>
      </div>
    </div>
  </div>
</div>

<?php include dirname(__FILE__) . '/../docs/api_footer.php';?>

</body>
</html>