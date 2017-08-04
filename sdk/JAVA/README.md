
## 使用说明
将框架目录下的 ./SDK/JAVA/net 目录中的全部代码拷贝到项目里面即可使用。如：  

![](http://7qnay5.com1.z0.glb.clouddn.com/qq20151017100539.jpg)  
  
##  代码示例
如下是使用的代码场景片段。  
  
首先，我们需要导入SDK包：
```java
//FullscreenActivity.java
import net.phalapi.sdk.*;
```
  
然后，准备一个子线程调用，并在此线程中实现接口请求：
```java
    /**
     * 网络操作相关的子线程
     */  
    Runnable networkTask = new Runnable() {  
      
        @Override  
        public void run() {  
            // TODO  
            // 在这里进行 http request.网络请求相关操作  
            
        	PhalApiClient client = PhalApiClient.create()
	       			    .withHost("http://demo.phalapi.net/");
	       	
	       	PhalApiClientResponse response = client
	       			    .withService("Default.Index")
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
```
  
接着，实现线程回调的hander：
```java
    Handler handler = new Handler() {  
        @Override  
        public void handleMessage(Message msg) {  
            super.handleMessage(msg);  
            Bundle data = msg.getData();  
            String val = data.getString("value");  
            Log.i("mylog", "请求结果为-->" + val);  
            // TODO  
            // UI界面的更新等相关操作  
        }  
    }; 
``` 
  
最后，在我们需要的地方启动：
```java
    View.OnClickListener mDummyBtnClickListener = new View.OnClickListener() {
        
        @Override
        public void onClick(View arg0) {
            // 开启一个子线程，进行网络操作，等待有返回结果，使用handler通知UI  
            new Thread(networkTask).start();  
            
            // ....
        }
    };
```

### 再一次调用和异常请求
当我们需要再次使用同一个接口实例进行请求时，需要先进行重置，以便清空之前的接口参数，如：
```java
//再一次请求
response = client.reset() //重置
		.withService("User.GetBaseInfo")
		.withParams("user_id", "1")
		.request();


content = "";
content += "ret=" + response.getRet() + "\n";
if (response.getRet() == 200) {
	try {
		JSONObject data = new JSONObject(response.getData());
		JSONObject info = new JSONObject(data.getString("info"));
		
		content += "data.info.id=" + info.getString("id") + "\n";
		content += "data.info.name=" + info.getString("name") + "\n";
		content += "data.info.from=" + info.getString("from") + "\n";
	} catch (JSONException ex) {
		  
	}
}
content += "msg=" + response.getMsg() + "\n";

Log.v("[PhalApiClientResponse]", content);
```
  
异常情况下，即ret != 200时，将返回错误的信息，如：
```java
//再来试一下异常的请求
response = client.reset()
		.withService("XXX.XXXX")
		.withParams("user_id", "1")
		.request();

content = "";
content += "ret=" + response.getRet() + "\n";
content += "msg=" + response.getMsg() + "\n";

Log.v("[PhalApiClientResponse]", content);
```
##  运行效果
运行后，查询log，可以看到：  

![](http://7qnay5.com1.z0.glb.clouddn.com/QQ20151017154114.jpg)  

  
可以注意到，在调试模式时，会有接口请求的链接和返回的结果日记，如：  
```
10-17 07:40:55.268: D/[PhalApiClient requestUrl](1376): http://demo.phalapi.net/?service=User.GetBaseInfo&user_id=1
10-17 07:40:55.364: D/[PhalApiClient apiResult](1376): {"ret":200,"data":{"code":0,"msg":"","info":{"id":"1","name":"dogstar","from":"oschina"}},"msg":""}
```

## 扩展你的过滤器和结果解析器
### (1)扩展过滤器
当服务端接口需要接口签名验证，或者接口参数加密传送，或者压缩传送时，可以实现此过滤器，以便和服务端操持一致。  
  
当需要扩展时，分两步。首先，需要实现过滤器接口：  
```java
class MyFilter implements PhalApiClientFilter {

        public void filter(String service, Map<String, String> params) {
            //TODO ...
        }
}
```
然后设置过滤器：
```java
PhalApiClientResponse response = PhalApiClient.create()
		   .withHost("http://demo.phalapi.net/")
		   .withFilter(new MyFilter())
		   // ...
		   .request();
```
### (2)扩展结果解析器
当返回的接口结果不是JSON格式时，可以重新实现此接口。  
  
当需要扩展时，同样分两步。类似过滤器扩展，这里不再赘述。
##  特别注意：Android之NetworkOnMainThreadException异常
由于此SDK包是使用HttpURLConnection发起请求时，所以在主线程调用时会触发NetworkOnMainThreadException异常，具体可参考： [Android之NetworkOnMainThreadException异常](http://blog.csdn.net/mad1989/article/details/25964495)  
  
所以，需要使用子线程来发起请求，或者重新继承改用异步的请求。  