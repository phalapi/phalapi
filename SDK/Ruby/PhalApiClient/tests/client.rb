require_relative '../phalapi_client'
require 'test/unit'

# open class
class PhalApi::Client
    attr_accessor :host, :service, :filter, :parser, :timeoutMs, :params
end

class ClientTest < Test::Unit::TestCase
    def setup
        @a_client = PhalApi::Client.create
    end

    def test_create
        assert_instance_of PhalApi::Client, @a_client
    end

    def test_with_host
        @a_client.withHost('http://demo')

        assert_equal 'http://demo', @a_client.host
    end

    def test_with_filter
        @a_client.withFilter(PhalApi::ClientFilter.new)

        assert_instance_of PhalApi::ClientFilter, @a_client.filter
    end

    def test_with_parser
        @a_client.withParser(PhalApi::ClientParserJson.new)

        assert_instance_of PhalApi::ClientParserJson, @a_client.parser
    end

    def test_with_service
        @a_client.withService('Default.Index')

        assert_equal 'Default.Index', @a_client.service
    end

    def test_with_timeoutMs
        @a_client.withTimeout(5000)

        assert_equal 5000, @a_client.timeoutMs
    end

    def test_with_more_params
        @a_client.withParams('username', 'dogstar')
        @a_client.withParams('version', '1.3.0')
        @a_client.withParams('age', 100)

        params = @a_client.params
        assert_equal 'dogstar', params['username']
        assert_equal 100, params['age']
    end

    def test_with_unexpected_params
        assert_raise (RuntimeError) {
            @a_client.withParams('mess')
        }
    end

    def test_reset
        @a_client.withHost('http://demo').withService('User.Move').withParams('id', '10');

        @a_client.reset
        assert_equal '', @a_client.service

        @a_client.withService('Default.Index').withParams('username', 'dogstar')
        assert_equal 'Default.Index', @a_client.service

        params = @a_client.params
        assert_equal 'dogstar', params['username']
        assert_equal nil, params['id']
    end

    def test_base_request
        a_response = @a_client.withHost('http://demo.phalapi.net').request

        assert_equal 200, a_response.ret
        assert_not_nil a_response.data
    end

    def test_normal_request
        a_response = @a_client.withHost('http://demo.phalapi.net').withService('Default.Index').withParams('username', 'dogstar').withParams('v', '1.3.0').request

        assert_equal 200, a_response.ret
        assert_not_nil a_response.data
    end

    def test_wrong_request
        a_response = @a_client.withHost('http://demo.phalapi.net').withService('XXXX.noThisMethod').request

        assert_equal 400, a_response.ret
    end

    def test_timeout_request
        a_response = @a_client.withHost('http://xxx.phalapi.net').withService('Default.Index').request

        assert_equal 408, a_response.ret
    end
end
