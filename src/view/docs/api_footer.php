  <div class="ui inverted vertical footer segment" style="margin-top:30px; background: #1B1C1D none repeat scroll 0% 0%;" >
    <div class="ui container">
      <div class="ui stackable inverted divided equal height stackable grid">
        <div class="ten wide column centered">
            <div class="column" align="center" >
                <img src="/phalapi_logo.png" alt="PhalApi">
            </div>
            <div class="column" align="center">
                <p>
                    <strong><?php echo \PhalApi\T('A PHP framework foucs on API fast development.'); ?></strong>
                    <br />
                    © 2015-<?php echo date('Y'); ?> Powered  By <a href="http://www.phalapi.net/" target=""><?php echo \PhalApi\T('PhalApi API Framework'), ' ', PHALAPI_VERSION; ?> </a> <span id="version_update"></span>
                    &nbsp;&nbsp;
                    <a target="_blank" href="http://pro.phalapi.net/" class="orange"><span class="ui tiny label orange"><i class="icon arrow alternate circle up"></i>专业版<span></a>
                </p>
            </div>
        </div>
      </div>
    </div>
  </div>

    <script type="text/javascript">
        let elem = document.getElementById('service');
        let sep = '_';
        let service = elem ? elem.value + sep : sep;

        function getData(apiMethod) {
            apiMethod = apiMethod ? apiMethod : 'POST';

            var data = new FormData();
            var param = [];
            $("td input").each(function(index,e) {
                if ($.trim(e.value)){
                    if (e.type != 'file'){
                        if ($(e).data('source') == 'get' || apiMethod == 'GET') {
                            param.push(e.name + '=' + encodeURIComponent(e.value));
                        } else {
                            data.append(e.name, e.value);
                        }

                        if (e.name !== "service" && e.value !== '') {
                            if (window.localStorage) {
                                localStorage.setItem(service + e.name, e.value);
                            } else {
                                $.cookie(service + e.name, e.value, {expires: 30});
                            }
                        }
                    } else{
                        var files = e.files;
                        if (files.length == 1){
                            data.append(e.name, files[0]);
                        } else{
                            for (var i = 0; i < files.length; i++) {
                                data.append(e.name + '[]', files[i]);
                            }
                        }
                    }
                }
            });
            param = param.join('&');
            return {param:param, data:data};
        }
        
        $(function(){
            $("#json_output").hide();
            $("#submit").on("click",function(){
                $("#json_output").html('<div class="ui active inverted dimmer"><div class="ui text loader">接口请求中……</div></div>');
                $("#json_output").show();

                var apiMethod = '<?php echo !empty($methods) && strtoupper($methods) == 'GET' ? 'GET' : 'POST'; ?>';

                var data = getData(apiMethod);
                var url_arr = $("input[name=request_url]").val().split('?');
                var url = url_arr.shift();
                var param = url_arr.join('?');
                param = param.length > 0 ? param + '&' + data.param : data.param;
                url = url + '?' + param;
                $.ajax({
                    url: url,
                    type: apiMethod,
                    data: apiMethod == 'GET' ? '' : data.data,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success:function(res,status,xhr){
                        console.log(xhr);
                        var statu = xhr.status + ' ' + xhr.statusText;
                        var header = xhr.getAllResponseHeaders();
                        var json_text = JSON.stringify(res, null, 4);    // 缩进4个空格
                        $("#json_output").html('<pre>' + statu + '<br/>' + header + '<br/>' + json_text + '</pre>');
                        $("#json_output").show();
                    },
                    error:function(error){
                        $("#json_output").html('接口请求失败：' + error);
                    }
                })
            })

            fillHistoryData();

            checkLastestVersion();

            // 选项卡
            $('.tabular.menu .item').tab();

        	$('.ui.dropdown').dropdown({
        	    // you can use any ui transition
        	    transition: 'drop'
        	  });
        })

        // 填充历史数据
        function fillHistoryData() {
            $("td input").each(function(index,e) {
                let cookie_value;
                if (window.localStorage) {
                    cookie_value = localStorage.getItem(service + e.name);
                } else {
                    cookie_value = $.cookie(service + e.name);
                }
                if (e.name !== "service" && cookie_value !== "" && cookie_value !== undefined) {
                    e.value = cookie_value;
                }
            });
        }

        // 检测最新版本
        function checkLastestVersion() {
                $.ajax({
                    url:'https://www.phalapi.net/check_lastest_version.php',
                    type:'get',
                    data:{version : '<?php echo PHALAPI_VERSION; ?>'},
                    success:function(res,status,xhr){
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

