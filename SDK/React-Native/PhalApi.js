/**
 * PhalApi框架 React-Naitve请求SDK
 *
 * "ダSimVlove辉"提供,QQ:254059780 有好的意见或建议请联系我 2016-03-08
 *
 * 分为3种请求方式:get,post
 *
 * 所有请求均统一传递4个参数值(请求地址,接口名称.请求参数GET传递拼接好的参数
 * Post传递数组key-value值,回调函数)
 *
 * 统一使用方式如下
 import PhalApi from 'sdk所在目录'
 
 const data = {user_id: "2"}
 
 PhalApi.apiPost("http://192.168.1.107/PhalApi_1.3.2/Public", "User.getBaseInfo",  data, (rs) => {
     if(rs.ret == 200){
         //成功处理
      }else{
         //失败处理
      }
 })
 
 * 如果想返回json  使用response.json()
 * 如果只要返回普通的string response.text()
 *
 */

// 配置调试
const debug = false;

export default PhalApi = new class {

/*
*   普通Post方式请求
*/
    apiPost(api_url, api_name, data, callback) {
        const textBody = this.urlForQuery(data)
        const full_api = api_url + "/" 
        const fetchOptions = {
            method: 'POST',
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: 'service=' + api_name + '&' + textBody
        };

        fetch(full_api, fetchOptions)
            .then(response => response.json())
            .then((responseText) => {
                callback(textBody)

                if (debug)
                    console.log(responseText);
            })
            .catch((error) => {
                if (debug)
                    console.warn(error);
            });
    }

/*
*   普通Get方式请求
*/
    apiGet(api_url, api_name, data, callback) {
        const textBody = this.urlForQuery(data)
        const full_api = api_url + "?service=" + api_name + "&" + textBody
        fetch(full_api)
            .then((response) => response.json())
            .then((responseText) => {
                callback(responseText)

                if (debug)
                    console.log(responseText);
            })
            .catch((error) => {
                if (debug)
                    console.warn(error);
            });

    }

// 相关参数的拼接
    urlForQuery(data) {
        const querystring = Object.keys(data)
            .map(key => key + '=' + encodeURIComponent(data[key]))
            .join('&');

        return querystring;
    }
}