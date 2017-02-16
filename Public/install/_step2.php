<?php defined('PHALAPI_INSTALL') || die('no access'); ?>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_header.php'; ?>

<div class="radius bg bouncein window window_big">
  <div class="window_title t_normal" >
    <span class="icon-circle"> </span>
    <span class="icon-circle"></span>
    <span class="margin-small-left">Installation Wizard</span>
  </div>
  <div class="padding-large text-black">
    <h1 class="margin-small-bottom" >System Configuration</h1>
    <h5 class="margin-big-bottom ">Please post configuration information of your system.</h5>
    <hr>
    <form class="form-horizontal" action="./?step=3" method="POST" >
      <div class="form-group">
        <div class="label">
          <label for="project">Project Name</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="project" size="30" placeholder="only letters, numbers and underscores" value="demo" />
        </div>
      </div>
      <hr>

      <div class="form-group">
        <div class="label">
          <label for="host">Database Server</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="host" size="30" placeholder="mostly is localhost" value="localhost" />
        </div>
      </div>

      <div class="form-group">
        <div class="label">
          <label for="user">Database Account</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="user" size="30" placeholder="mostly is root" value="root" />
        </div>
      </div>

      <div class="form-group">
        <div class="label">
          <label for="username">Database Password</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="password" size="30" placeholder=""/>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label for="port">Database Port</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="port" size="30" placeholder="no need to modify generally" value="3306"/>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label for="name">Database Name</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="name" size="30" placeholder="need to create database manully if not exsist" value="phalapi_demo"/>
        </div>
      </div>

      <div class="form-group">
        <div class="label">
          <label for="prefix">Database Table Prefix</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="prefix" size="30" placeholder="no need to modify generally" value="pa_"/>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label for="charset">Database Charset</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="charset" size="30" placeholder="no need to modify generally" value="UTF8"/>
        </div>
      </div>
<!--      <hr>-->
<!--      <div class="form-group">-->
<!--        <div class="label">-->
<!--          <label for="db_engine">接口签名方案</label>-->
<!--        </div>-->
<!--        <div class="field">-->
<!--          <select name="db_engine" class="form-control"><option value="zh_cn">无接口签名验证</option><option value="en">MD5验签</option>><option value="en">自定义难签</option></select>-->
<!--        </div>-->
<!--      </div>-->
<!--      <hr>-->
<!--      <div class="form-group">-->
<!--        <div class="label">-->
<!--          <label for="db_engine">Language</label>-->
<!--        </div>-->
<!--        <div class="field">-->
<!--          <select name="db_engine" class="form-control"><option value="zh_cn">中文</option><option value="en">English</option></select>        </div>-->
<!--      </div>-->
      <hr>
      <div class="margin-big-top" >
        <button type="submit" class="button bg-yellow margin-small-right" name="doSubmit" value="ok" >  Start Installing  </button>
        <a class="button  margin-small-right"  href="./?step=1" role="button">  Back  </a>
      </div>
    </form>
  </div>
</div>
</div>

<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_footer.php'; ?>
