using Newtonsoft.Json;
using System;

namespace PhalApiClientSDK
{
    /**
    * JSON解析
    */
    public class PhalApiClientParserJson : PhalApiClientParser {

	    public PhalApiClientResponse parse(String apiResult) {
		    if (apiResult == null) {
                return new PhalApiClientResponse(408, "", "Request Timeout");
		    }
		
		    try {

                return JsonConvert.DeserializeObject<PhalApiClientResponse>(apiResult);
		    } catch (Exception ex) {
			    return new PhalApiClientResponse(500, "", "Internal Server Error: " + ex.Message);
		    }
	    }
    }
}
