package net.phalapi.sdk;

import org.json.JSONObject;

/**
 * JSON解析
 */
public class PhalApiClientParserJson implements PhalApiClientParser {

	public PhalApiClientResponse parse(String apiResult) {
		if (apiResult == null) {
			return new PhalApiClientResponse(408, "", "Request Timeout");
		}
		
		try {
			JSONObject jsonObj = new JSONObject(apiResult);
			
			return new PhalApiClientResponse(
					jsonObj.getInt("ret"), jsonObj.getString("data"), jsonObj.getString("msg"));
		} catch (Exception ex) {
			return new PhalApiClientResponse(500, "", "Internal Server Error Or " + ex.getMessage());
		}
	}
}
