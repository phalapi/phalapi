require 'test_helper'

class ClientResponseTest < Minitest::Test
    def test_new
        a_response = PhalApi::Client::Response.new(400)
        assert_equal 400, a_response.ret

        a_response = PhalApi::Client::Response.new(500, [], 'Server Error')
        assert_equal 500, a_response.ret
        assert_equal [], a_response.data
        assert_equal 'Server Error', a_response.msg
    end
end
