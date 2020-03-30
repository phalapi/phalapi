PhalApiClientResponse response = PhalApiClient.create()
  .withHost("http://demo.phalapi.net/")
  .withService("Default.Index")
  .withparamsList("name", "dogstar")
  .withTimeout(3000)
  .request();

Log.v("response ret", response.ret + "");
Log.v("response data", response.data);
Log.v("response msg", response.msg);