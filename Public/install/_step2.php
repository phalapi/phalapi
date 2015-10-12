<?php defined('PHALAPI_INSTALL') || die('no access'); ?>
<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_header.php'; ?>

      <div class="row">
        <h1>配置系统</h1>
        <p class="lead">需要您提供必要的系统配置信息</p>

        <br />

        <form class="form-horizontal" action="./?step=3" method="POST" >
          <div class="form-group">
            <label class="col-sm-2 control-label">项目名称</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" placeholder="只能是字母、数字、下划线的组合" name="project" value="demo" >
            </div>
          </div>

            <hr />

          <div class="form-group">
            <label class="col-sm-2 control-label">MySql数据库服务器</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" placeholder="通常为 localhost" name="host" value="localhost" >
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">MySql数据库帐号</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" placeholder="通常为 root" name="user" value="root" >
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">MySql数据库密码</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" placeholder="" name="password" >
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">MySql数据库端口</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" placeholder="一般情况下不需要修改" name="port" value="3306" >
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">MySql数据库名称</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" placeholder="数据库不存在时需要先创建" name="name" >
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">MySql数据库表前缀</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" placeholder="同数据库安装多个本程序时需要更改" name="prefix" value="phalapi_" >
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">MySql数据库编码</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="一般情况下不需要修改" name="charset" value="UTF8" >
            </div>
          </div>

            <hr />

          <div class="form-group">
            <label class="col-sm-2 control-label">接口签名方案</label>
            <div class="col-sm-10">
                <select name="db_engine" class="form-control"><option value="zh_cn">无接口签名验证</option><option value="en">MD5验签</option>><option value="en">自定义难签</option></select>
            </div>
          </div>

            <hr />

          <div class="form-group">
            <label class="col-sm-2 control-label">翻译语言</label>
            <div class="col-sm-10">
                <select name="db_engine" class="form-control"><option value="zh_cn">中文</option><option value="en">English</option></select>
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn btn-success">开始安装</button>
            </div>
          </div>
        </form>

      </div>

<?php include dirname(__FILE__) . DIRECTORY_SEPARATOR . '_footer.php'; ?>
