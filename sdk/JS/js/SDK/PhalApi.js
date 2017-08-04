/**
 * PhalApi框架 JS请求SDK
 *
 * "猫了_个咪"提供,博客地址w-blog.cn
 * 有好的意见或建议请联系我-><wenzhenxi@vip.qq.com> 2015-10-20
 *
 * 分为3种请求方式:get,post和get_jsonp
 *
 * 所有请求均统一传递4个参数值(请求地址,接口名称.请求参数GET传递拼接好的参数
 * Post传递数组key-value值,回调函数)
 *
 * 统一使用方式如下
 * var url = '';
 * var api = '';
 * var data = '';
 * query_get(url, api, data, function(rs){
 *     //回调函数 rs为返回结果已经反json化
 *     if(rs.ret == 200){
 *        成功处理
 *     }else{
 *        失败处理
 *     }
 *   });
 *
 */

//-------------------------------配置项------------------------------------
var debug = true; //调试模式
//-------------------------------配置项------------------------------------

/**
 *  普通的post请求方法
 **/
function query_post(api_url, api_name, data, callback){
  //拼接请求的URL地址
  var fullapi = api_url + '?service=' + api_name;
  //执行请求
  $.ajax({
    url        : fullapi,  //请求地址
    method     : 'POST',   //请求方式
    crossDomain: true,
    data       : data,     //请求参数
    complete   : function(rs){
      //反Json化
      rs = JSON.parse(rs.response || rs.responseText);
      //把返回结果返回到控制台(debug模式自动开启)
      if(debug == true){
        console.log(fullapi, 'back', rs);
      }
      //回调函数
      callback(rs);
    }
  });
}

/**
 *  普通的get请求方法
 **/
function query_get(api_url, api_name, data, callback){
  //拼接请求的URL地址
  var fullapi = api_url + '?service=' + api_name + data;
  //执行请求
  $.ajax({
    url     : fullapi,  //请求地址
    method  : 'GET',   //请求方式
    complete: function(rs){
      //反Json化
      rs = JSON.parse(rs.response || rs.responseText);
      //把返回结果返回到控制台(debug模式自动开启)
      if(debug == true){
        console.log(fullapi, 'back', rs);
      }
      //回调函数
      callback(rs);
    }
  });
}

/**
 *  JsonP请求方法(用于跨域请求,只能进行get请求)
 **/
function query_jsonp(api_url, api_name, data, callback){
  //拼接请求的URL地址(&callback=1是Phalapi默认使用JsonP格式)
  var fullapi = api_url + '?service=' + api_name + '&callback=1' + data;
  //执行请求
  $.ajax({
    type    : "get",
    async   : false,
    url     : fullapi,    //请求参数
    dataType: "jsonp",
    jsonp   : "callback", //传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
    success : function(rs){
      //把返回结果返回到控制台(debug模式自动开启)
      if(debug == true){
        console.log(fullapi, 'back', rs);
      }
      //回调函数
      callback(rs);
    },
    error   : function(error){
      alert('fail');
    }
  });
}

