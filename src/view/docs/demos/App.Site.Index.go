func main() {
	rs, err := PhalApiClient.NewRequest().
		WithHost(`{url}`).
		WithService("{s}").
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