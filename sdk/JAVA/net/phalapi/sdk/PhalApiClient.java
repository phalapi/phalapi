package net.phalapi.sdk;

import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;
import java.util.Map.Entry;

import org.json.JSONObject;

import android.util.Log;

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
 *   .withParams("name", "dogstar")
 *   .withTimeout(3000)
 *   .request();
 *
 * Log.v("response ret", response.getRet() + "");
 * Log.v("response data", response.getData());
 * Log.v("response msg", response.getMsg());
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
    protected Map<String, String> params;

    /**
     * 创建一个接口实例，注意：不是单例模式
     * @return PhalApiClient
     */
    public static PhalApiClient create() {
        return new PhalApiClient();
    }

    protected PhalApiClient() {
    	this.host = "";
    	this.reset();

        this.parser = new PhalApiClientParserJson();
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
        this.params = new HashMap<String, String>();
        
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
        this.params.put(name, value);
        return this;
    }

    /**
     * 设置超时时间，单位毫秒
     * @param int timeoutMS 超时时间，单位毫秒
     * @return PhalApiClient
     */
    public PhalApiClient withTimeout(int timeoutMS) {
        this.timeoutMs = timeoutMS;
        return this;
    }

    /**
     * 发起接口请求
     * @return PhalApiClientResponse
     */
    public PhalApiClientResponse request() {
        String url = this.host;

        if (this.service != null && this.service.length() > 0) {
            url += "?service=" + this.service;
        }
        if (this.filter != null) {
            this.filter.filter(this.service, this.params);
        }

        try {
        	String rs = this.doRequest(url, this.params, this.timeoutMs);
        	return this.parser.parse(rs);
        } catch (Exception ex) {
        	return new PhalApiClientResponse(408, "", ex.getMessage());
        }
    }
    
	protected String doRequest(String requestUrl, Map<String, String> params, int timeoutMs) throws Exception {
		String result = null;
		URL url = null;
		HttpURLConnection connection = null;
		InputStreamReader in = null;
		
		url = new URL(requestUrl);
		connection = (HttpURLConnection) url.openConnection();
		connection.setDoInput(true);
		connection.setDoOutput(true);
		connection.setRequestMethod("POST"); // 请求方式
		connection.setUseCaches(false);
		connection.setConnectTimeout(timeoutMs);
		
        DataOutputStream out = new DataOutputStream(connection.getOutputStream());
        
        //POST参数
        String postContent = "";
        Iterator<Entry<String, String>> iter = params.entrySet().iterator();
        while (iter.hasNext()) {
        	Map.Entry<String, String> entry = (Map.Entry<String, String>) iter.next(); 
        	postContent += "&" + entry.getKey() + "=" + entry.getValue();
        }
        out.writeBytes(postContent);
        out.flush();
        out.close();

		Log.d("[PhalApiClient requestUrl]", requestUrl + postContent);
        
		in = new InputStreamReader(connection.getInputStream());
		BufferedReader bufferedReader = new BufferedReader(in);
		StringBuffer strBuffer = new StringBuffer();
		String line = null;
		while ((line = bufferedReader.readLine()) != null) {
			strBuffer.append(line);
		}
		result = strBuffer.toString();
		
		Log.d("[PhalApiClient apiResult]", result);
		
		if (connection != null) {
			connection.disconnect();
		}
		
		if (in != null) {
			try {
				in.close();
			} catch (IOException e) {
				e.printStackTrace();
			}
		}

		return result;
	}
}