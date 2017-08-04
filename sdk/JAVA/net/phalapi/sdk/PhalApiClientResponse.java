package net.phalapi.sdk;

/**
 * 接口返回结果
 *
 * - 与接口返回的格式对应，即有：ret/data/msg
 */
public class PhalApiClientResponse {

    protected int ret;
    protected String data;
    protected String msg;

    /**
     * 完全构造函数
     * @param int ret
     * @param String data
     * @param String msg
     */
    public PhalApiClientResponse(int ret, String data, String msg) {
        this.ret = ret;
        this.data = data;
        this.msg = msg;
    }

    public PhalApiClientResponse(int ret, String data) {
    	this(ret, data, "");
    }
    
    public PhalApiClientResponse(int ret) {
    	this(ret, "", "");
    }
    
    public int getRet() {
        return this.ret;
    }

    public String getData() {
        return this.data;
    }

    public String getMsg() {
        return this.msg;
    }
}