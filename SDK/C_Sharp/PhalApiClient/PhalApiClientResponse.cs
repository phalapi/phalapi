using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PhalApiClientSDK
{
    /**
     * 接口返回结果
     *
     * - 与接口返回的格式对应，即有：ret/data/msg
     */
    public class PhalApiClientResponse
    {

        public int ret
        {
            get {return ret;}
            set {ret = value;}
        }

        public String data
        {
            get { return data; }
            set { data = value; }
        }

        public String msg
        {
            get { return msg; }
            set { msg = value; }
        }

        /**
         * 完全构造函数
         * @param int ret
         * @param JSONObject data
         * @param String msg
         */
        public PhalApiClientResponse(int ret, String data, String msg)
        {
            this.ret = ret;
            this.data = data;
            this.msg = msg;
        }

        public PhalApiClientResponse(int ret, String data)
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
    }
}
