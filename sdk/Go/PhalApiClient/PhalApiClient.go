package PhalApiClient

import (
	"encoding/json"
	"errors"
	"io/ioutil"
	"net"
	"net/http"
	"net/url"
	"time"
)

/**
 * PhalApi客户端SDK包（Go版）
 *
 *
 * <br>使用示例：<br>
 ```
	rs, err := PhalApiClient.NewRequest().
		WithHost(`http://127.0.0.1/PhalApi/Public/index.php`).
		WithService("Default.index").
		WithParams(url.Values{}).
		Get()
	if err != nil {
		fmt.Println(err.Error())
	} else {
		fmt.Println("code------------------------", rs.Code)
		fmt.Println("data------------------------", rs.Data)
		fmt.Println("msg------------------------", rs.Msg)
	}
```
    *
    * @package     PhalApi\SDK
    * @author      prettyyjnic <prettyyjnic@qq.com> 2016-03-09
*/

type request struct {
	Host    string
	Service string
	Params  url.Values
	Timeout time.Duration
}

type result struct {
	Code int64       `json:"ret"`
	Data interface{} `json:"data"`
	Msg  interface{} `json:"msg"`
}

func NewRequest() *request {
	ptr_request := new(request)
	return ptr_request
}

func (this *request) WithHost(host string) *request {
	this.Host = host
	return this
}

func (this *request) WithService(service string) *request {
	this.Service = service
	return this
}

func (this *request) WithParams(params url.Values) *request {
	this.Params = params
	return this
}

func (this *request) WithTimeout(timeout time.Duration) *request {
	this.Timeout = timeout
	return this
}

func (this *request) Reset() {
	this.Params = url.Values{}
	this.Host = ""
	this.Service = ""
	this.Timeout = 0
}

func (this *request) Get() (*result, error) {
	client := this.getClient()
	var str_url string
	if this.Service != "" {
		this.Params.Add("service", this.Service)
	}
	str_url = this.Host + "?" + this.Params.Encode()
	resp, err := client.Get(str_url)
	if err != nil {
		return nil, err
	} else {
		defer resp.Body.Close()
		return dealResult(resp)
	}

}

func (this *request) Post() (*result, error) {
	client := this.getClient()
	var str_url string
	if this.Service != "" {
		str_url = this.Host + "?service=" + this.Service
	} else {
		str_url = this.Host
	}
	resp, err := client.PostForm(str_url, this.Params)
	if err != nil {
		return nil, err
	} else {
		defer resp.Body.Close()
		return dealResult(resp)
	}
}

func dealResult(response *http.Response) (*result, error) {
	if response.Status == "200 OK" {
		ret := new(result)
		body, _ := ioutil.ReadAll(response.Body)
		json.Unmarshal(body, ret)
		return ret, nil
	} else {
		return nil, errors.New(response.Status)
	}
}

func (this *request) getClient() *http.Client {
	tr := &http.Transport{
		Dial: func(netw, addr string) (net.Conn, error) {
			if this.Timeout == 0 {
				this.Timeout = time.Second * 3
			}
			conn, err := net.DialTimeout(netw, addr, this.Timeout)
			if err != nil {
				return nil, err
			}
			conn.SetDeadline(time.Now().Add(this.Timeout))
			return conn, nil
		},
		ResponseHeaderTimeout: this.Timeout,
	}
	client := &http.Client{Transport: tr}
	return client
}
