<?php defined('PHALAPI_INSTALL') || die('no access'); ?>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_header.php'; ?>

<div class="radius bg bouncein window window_big">
  <div class="window_title t_normal" >
    <span class="icon-circle"> </span>
    <span class="icon-circle"></span>
    <span class="margin-small-left">安装向导</span>
  </div>
  <div class="padding-large text-black">
    <h1 class="margin-small-bottom" >配置系统</h1>
    <h5 class="margin-big-bottom ">需要您提供必要的系统配置信息</h5>
    <hr>
    <form class="form-horizontal" action="./?step=3" method="POST" >
      <div class="form-group">
        <div class="label">
          <label for="project">项目名称</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="project" size="30" placeholder="只能是字母、数字、下划线的组合" value="demo" />
        </div>
      </div>
      <hr>

      <div class="form-group">
        <div class="label">
          <label for="host">数据库服务器</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="host" size="30" placeholder="通常为 localhost" value="localhost" />
        </div>
      </div>

      <div class="form-group">
        <div class="label">
          <label for="user">数据库帐号</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="user" size="30" placeholder="通常为 root" value="root" />
        </div>
      </div>

      <div class="form-group">
        <div class="label">
          <label for="username">数据库密码</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="password" size="30" placeholder=""/>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label for="port">数据库端口</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="port" size="30" placeholder="一般情况下不需要修改" value="3306"/>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label for="name">数据库名称</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="name" size="30" placeholder="数据库不存在时需要先创建" value="phalapi_demo"/>
        </div>
      </div>

      <div class="form-group">
        <div class="label">
          <label for="prefix">数据库表前缀</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="prefix" size="30" placeholder="同数据库安装多个本程序时需要更改" value="pa_"/>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label for="charset">数据库编码</label>
        </div>
        <div class="field">
          <input type="text" class="input" name="charset" size="30" placeholder="一般情况下不需要修改" value="UTF8"/>
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
<!--          <label for="db_engine">翻译语言</label>-->
<!--        </div>-->
<!--        <div class="field">-->
<!--          <select name="db_engine" class="form-control"><option value="zh_cn">中文</option><option value="en">English</option></select>        </div>-->
<!--      </div>-->
      <hr>
      <div class="margin-big-top" >
        <button type="submit" class="button bg-yellow margin-small-right" name="doSubmit" value="ok" >  开始安装  </button>
        <a class="button  margin-small-right"  href="./?step=1" role="button">  上一步  </a>
      </div>
    </form>
  </div>
</div>
</div>

<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_footer.php'; ?>
