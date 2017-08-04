

using System;
using System.Collections.Generic;
using System.Text;
using System.Net;
using System.IO;

namespace PhalApiClientSDK
{

    /**
     * PhalApi客户端SDK包（JAVA版）
     *
     * - 以接口查询语言（ASQL）的方式来实现接口请求
     * - 出于简明客户端，将全部的类都归于同一个文件，避免过多的加载
     * 
     * <br>使用示例：<br>
     ```
     * PhalApiClientResponse response = PhalApiClient.create()
     *   .withHost("http://demo.phalapi.net/")
     *   .withService("Default.Index")
     *   .withparamsList("name", "dogstar")
     *   .withTimeout(3000)
     *   .request();
     *
     * Log.v("response ret", response.ret + "");
     * Log.v("response data", response.data);
     * Log.v("response msg", response.msg);
    ```
        *
        * @package     PhalApi\Response
        * @license     http://www.phalapi.net/license GPL 协议
        * @link        http://www.phalapi.net/
        * @author      dogstar <chanzonghuang@gmail.com> 2015-10-16
     */

    public class PhalApiClient {

        protected String host;
        protected PhalApiClientFilter filter;
        protected PhalApiClientParser parser;
        protected String service;
        protected int timeoutMs;
        protected Dictionary<String, String> paramsList;

        /**
         * 创建一个接口实例，注意：不是单例模式
         * @return PhalApiClient
         */
        public static PhalApiClient create() {
            return new PhalApiClient();
        }

        protected PhalApiClient() {
    	    this.host = "";
            this.parser = new PhalApiClientParserJson();

            this.reset();
        }

        /**
         * 设置接口域名
         * @param String host
         * @return PhalApiClient
         */
        public PhalApiClient withHost(String host) {
            this.host = host;
            return this;
        }

        /**
         * 设置过滤器，与服务器的DI().filter对应
         * @param PhalApiClientFilter filter 过滤器
         * @return PhalApiClient
         */
        public PhalApiClient withFilter(PhalApiClientFilter filter) {
            this.filter = filter;
            return this;
        }

        /**
         * 设置结果解析器，仅当不是JSON返回格式时才需要设置
         * @param PhalApiClientParser parser 结果解析器
         * @return PhalApiClient
         */
        public PhalApiClient withParser(PhalApiClientParser parser) {
            this.parser = parser;
            return this;
        }

        /**
         * 重置，将接口服务名称、接口参数、请求超时进行重置，便于重复请求
         * @return PhalApiClient
         */
        public PhalApiClient reset() {
            this.service = "";
            this.timeoutMs = 3000;
            this.paramsList = new Dictionary<String, String>();
        
            return this;
        }

        /**
         * 设置将在调用的接口服务名称，如：Default.Index
         * @param String service 接口服务名称
         * @return PhalApiClient
         */
        public PhalApiClient withService(String service) {
            this.service = service;
            return this;
        }

        /**
         * 设置接口参数，此方法是唯一一个可以多次调用并累加参数的操作
         * @param String name 参数名字
         * @param String value 值
         * @return PhalApiClient
         */
        public PhalApiClient withParams(String name, String value) {
            this.paramsList.Add(name, value);
            return this;
        }

        /**
         * 设置超时时间，单位毫秒
         * @param int timeoutMS 超时时间，单位毫秒
         * @return PhalApiClient
         */
        public PhalApiClient withTimeout(int timeoutMs) {
            this.timeoutMs = timeoutMs;
            return this;
        }

        /**
         * 发起接口请求
         * @return PhalApiClientResponse
         */
        public PhalApiClientResponse request() {
            String url = this.host;

            if (this.service != null && this.service.Length > 0) {
                url += "?service=" + this.service;
            }
            if (this.filter != null) {
                this.filter.filter(this.service, this.paramsList);
            }

            try {
        	    String rs = this.doRequest(url, this.paramsList, this.timeoutMs);
        	    return this.parser.parse(rs);
            } catch (Exception ex) {
        	    //return new PhalApiClientResponse(408, new JSONObject(), ex.Message);
                return new PhalApiClientResponse(408); //TODO
            }
        }
    
	    protected String doRequest(String requestUrl, Dictionary<String, String> paramsList, int timeoutMs) {
		    String result = null;
            Encoding encoding = Encoding.Default;

            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(requestUrl);

            request.Method = "post";

            request.Accept = "text/html, application/xhtml+xml, */*";

            request.ContentType = "application/x-www-form-urlencoded";

            String strPostdata = "";
            //KeyValuePair<T,K>
            foreach (KeyValuePair<String, String> kv in paramsList)
            {
                strPostdata += "&" + kv.Key + "=" + kv.Value;
            }
            byte[] buffer = encoding.GetBytes(strPostdata);

            request.ContentLength = buffer.Length;

            request.GetRequestStream().Write(buffer, 0, buffer.Length);

            HttpWebResponse response = (HttpWebResponse)request.GetResponse();

            using (StreamReader reader = new StreamReader(response.GetResponseStream(), System.Text.Encoding.GetEncoding("utf-8")))
            {

                return reader.ReadToEnd();
            }  

		   
	    }
    }
}
