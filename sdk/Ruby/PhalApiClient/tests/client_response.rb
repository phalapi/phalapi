require_relative '../phalapi_client'
require 'test/unit'

class ClientResponseTest < Test::Unit::TestCase
    def test_new
        a_response = PhalApi::ClientResponse.new(400)
        assert_equal 400, a_response.ret

        a_response = PhalApi::ClientResponse.new(500, [], 'Server Error')
        assert_equal 500, a_response.ret
        assert_equal [], a_response.data
        assert_equal 'Server Error', a_response.msg
    end
end
