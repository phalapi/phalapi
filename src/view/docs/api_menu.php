
  <div class="ui fixed inverted menu blue">
    <div class="ui container">
      <a href="/" class="header item">
        <img class="logo" alt="phalapi" src="/phalapi_logo.png">
        <?php echo $projectName; ?>
      </a>
      
          <a href="/" class="item"><?php echo \PhalApi\T('Home'); ?></a>
      	  <a href="/docs.php" class="item"><i class="globe icon"></i><?php echo \PhalApi\T('API Docs'); ?></a>
     	 <a href="http://docs.phalapi.net/#/v2.0/" class="item" target="_blank"><i class="file alternate outline icon"></i> <?php echo \PhalApi\T('Dev Docs'); ?></a>
      	<a href="/portal/" class="item" target="_blank"><i class="chart line icon"></i> <?php echo \PhalApi\T('Portal'); ?></a>

         <div class="item">
             <div class="ui form">
             <form action="/docs.php?search=k" method="get" target="_blank">
                 <input type="text" name="keyword" placeholder="<?php echo \PhalApi\T('Search API'); ?>" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
             </form>
             </div>
         </div>
         
     <div class="right menu">
         <div class="ui dropdown item">
         <?php echo \PhalApi\T('Language'); ?> <i class="dropdown icon"></i>
          <div class="menu">
              <a class="item" href="/docs.php?language=zh_cn" >简体中文</a>
              <a class="item" href="/docs.php?language=en" >English</a>
          </div>
        </div>
      </div>
              <!-- 欢迎/注册 -->
    <?php if (\PhalApi\DI()->admin && \PhalApi\DI()->admin->check(false)) { ?>
    <a class="item" target="_blank" href="/portal" ><?php echo \PhalApi\T('welcome'); ?>: <?php echo \PhalApi\DI()->admin->username; ?></a>
      <?php } else { ?>
      
        <div class="item">
		  <div class="ui green button"><a style="color:#fff;"  href="/portal" target="_blank"><i class="user icon"></i><?php echo \PhalApi\T('Sign In'); ?></a></div>
        </div>
    <?php } ?>
    </div>
  </div>
  

<div class="row" style="margin-top: 60px;" ></div>
