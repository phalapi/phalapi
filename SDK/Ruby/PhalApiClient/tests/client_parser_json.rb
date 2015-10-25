require_relative '../phalapi_client'
require 'test/unit'

class ClientParserJsonTest < Test::Unit::TestCase
    def setup
        @parser = PhalApi::ClientParserJson.new
    end

    def test_nil
        rs = nil
        a_response = @parser.parse(rs)

        assert_equal 408, a_response.ret
    end

    def test_illegal_json
        rs = 'i am not a json, as you can see'
        a_response = @parser.parse(rs)

        assert_equal 500, a_response.ret
    end

    def test_normal_json
        rs = '{"ret":200,"data":{"title":"Hello World","content":"Welcome to use Web Tools!","version":"1.0.0","time":1415982826},"msg":""}'
        a_response = @parser.parse(rs)

        assert_equal 200, a_response.ret

        data = a_response.data
        assert_equal 'Hello World', data['title']
        assert_equal '1.0.0', data['version']

        assert_equal '', a_response.msg
    end
end
