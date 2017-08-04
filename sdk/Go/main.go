package main

import (
	"./PhalApiClient"
	"fmt"
	"net/url"
)

func main() {
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
}
