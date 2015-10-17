using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace PhalApiClientSDK
{
    /**
    * 接口结果解析器
    * 
    * - 可用于不同接口返回格式的处理
    */
    public interface PhalApiClientParser
    {

        /**
         * 结果解析
         * @param String apiResult
         * @return PhalApiClientResponse
         */
        PhalApiClientResponse parse(String apiResult);
    }


}
