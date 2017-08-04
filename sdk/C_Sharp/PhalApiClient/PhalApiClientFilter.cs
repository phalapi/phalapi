using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PhalApiClientSDK
{
    /**
     * 接口过滤器
     * 
     * - 可用于接口签名生成
     */
    public interface PhalApiClientFilter {

        /**
         * 过滤操作
         * @param string service 接口服务名称
         * @param Map<String, String> params 接口参数，注意是引用。可以直接修改
         * @return null
         */
	    void filter(String service, Dictionary<String, String> paramsList);
    }

}
