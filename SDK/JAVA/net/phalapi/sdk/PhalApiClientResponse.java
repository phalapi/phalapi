package net.phalapi.sdk;

import org.json.JSONObject;

/**
 * 接口返回结果
 *
 * - 与接口返回的格式对应，即有：ret/data/msg
 */
public class PhalApiClientResponse {

    protected int ret;
    protected JSONObject data;
    protected String msg;

    /**
     * 完全构造函数
     * @param int ret
     * @param JSONObject data
     * @param String msg
     */
    public PhalApiClientResponse(int ret, JSONObject data, String msg) {
        this.ret = ret;
        this.data = data;
        this.msg = msg;
    }

    public PhalApiClientResponse(int ret, JSONObject data) {
    	this(ret, data, "");
    }
    
    public PhalApiClientResponse(int ret) {
    	this(ret, new JSONObject(), "");
    }
    
    public int getRet() {
        return this.ret;
    }

    public JSONObject getData() {
        return this.data;
    }

    public String getMsg() {
        return this.msg;
    }
}