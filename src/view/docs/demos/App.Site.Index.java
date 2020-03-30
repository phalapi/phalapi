    /**
     * 网络操作相关的子线程
     */  
    Runnable networkTask = new Runnable() {  
      
        @Override  
        public void run() {  
            // TODO  
            // 在这里进行 http request.网络请求相关操作  
            
        	PhalApiClient client = PhalApiClient.create()
	       			    .withHost("{url}");
	       	
	       	PhalApiClientResponse response = client
	       			    .withService("{s}")
	       			    .withParams("username", "dogstar")
	       			    .withTimeout(3000)
	       			    .request();

	   		String content = "";
	   		content += "ret=" + response.getRet() + "\n";
	   		if (response.getRet() == 200) {
				try {
					JSONObject data = new JSONObject(response.getData());
					content += "data.title=" + data.getString("title") + "\n";
					content += "data.content=" + data.getString("content") + "\n";
					content += "data.version=" + data.getString("version") + "\n";
				} catch (JSONException ex) {
					  
				}
	   		}
			content += "msg=" + response.getMsg() + "\n";
			
			Log.v("[PhalApiClientResponse]", content);
            
            
            Message msg = new Message();  
            Bundle data = new Bundle();  
            data.putString("value", content);  
            msg.setData(data);  
            handler.sendMessage(msg);  
        }  
    }; 