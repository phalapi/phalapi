using System;

namespace PhalApiClientSDK
{
    /**
     * 接口返回结果
     *
     * - 与接口返回的格式对应，即有：ret/data/msg
     */
    public class PhalApiClientResponse
    {
        public int ret { get; set; }

        public dynamic data { get; set; }

        public String msg { get; set; }

        

        /**
         * 完全构造函数
         * @param int ret
         * @param JSONObject data
         * @param String msg
         */
        public PhalApiClientResponse(int ret, dynamic data, String msg)
        {
            this.ret = ret;
            this.data = data;
            this.msg = msg;
        }

        public PhalApiClientResponse(int ret, dynamic data)
        {
            this.ret = ret;
            this.data = data;
            this.msg = "";
        }

        public PhalApiClientResponse(int ret)
        {
            this.ret = ret;
            this.data = "";
            this.msg = "";
        }
        public PhalApiClientResponse()
        {
            
        }
    }
}
