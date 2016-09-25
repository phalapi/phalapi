require 'open-uri'
require 'net/http'
require 'json'

# PhalApi 客户端SDK包(Ruby版)
#
# - 以接口查询语言（ASQL）的方式来实现接口请求
# - 出于简明客户端，将全部的类都归于同一个文件，避免过多的加载
#
# <br>使用示例：<br>
# ```
# a_response = PhalApi::Client.create \
#   .withHost('http://demo.phalapi.net') \
#   .withService('Default.Index') \
#   .withParams('username', 'dogstar') \
#   .withTimeout(3000) \
#   .request
#
# puts a_response.ret, a_response.data, a_response.msg
#
# ```
#
# @package     PhalApi\SDK
# @license     http://www.phalapi.net/license GPL 协议
# @link        http://www.phalapi.net/
# @author      dogstar <chanzonghuang@gmail.com> 2015-10-25

module PhalApi

    # PhalApi::Client 客户端入口类
    # 完成总的调用
    class Client
        def self.create
            self.new
        end

        def method_missing(name, *args, &block)
            raise "undefined method `#{name}' for PhalApi::Client" if "with" != name[0,4].downcase

            param_name = name[4, name.length].downcase

            case param_name
            when 'host'
                @host = args[0]
            when 'filter'
                @filter = args[0]
            when 'parser'
                @parser = args[0]
            when 'service'
                @service = args[0]
            when 'timeout'
                @timeoutMs = args[0].to_i
            else 'params'
                raise "you forget a value for param: #{args[0]} ?" if args[1] == nil #warm ?
                @params[args[0]] = args[1] 
            end

            self
        end

        def initialize
            @host = ''

            reset

            @parser = PhalApi::ClientParserJson.new
        end

        def reset
            @service, @timeoutMs = '', 3000
            @params = Hash.new
            self
        end

        # 发起接口请求
        def request
            url = @host

            url += "?service=" + @service if @service != nil and @service != ''

            @filter.send :filter, @service, @params if @filter != nil

            begin
                rs = do_request url, @params, @timeoutMs
                return @parser.parse rs
            rescue Exception => e
                return PhalApi::ClientResponse.new(408, [], e.message)
            end
        end

        def do_request(url, params, timeoutMs)
            uri = URI.parse(url)
            res = Net::HTTP.post_form(uri, params)   
            # TODO: timeoutMs ?

            case res
            when Net::HTTPSuccess
                return res.body
            else
                return nil
            end
        end
    end

    # 接口返回结果
    # 
    # - 与接口返回的格式对应，即有：ret/data/msg
    class ClientResponse
        def initialize(ret, data = nil, msg = nil)
            @ret, @data, @msg = ret, data, msg
        end

        def ret
            @ret
        end

        def data
            @data
        end
        
        def msg
            @msg
        end

    end

    # 接口结果解析器
    # 
    # - 可用于不同接口返回格式的处理
    class ClientParser
        def parse(rs)
            raise 'hey guys, you should rewrite PhalApi::ClientPaser.parse'
        end
    end

    # JSON解析
    class ClientParserJson < PhalApi::ClientParser
        def parse(rs)
            #puts "what we got: #{rs}"
            return PhalApi::ClientResponse.new(408, [], 'Request Timeout') if rs == nil

            begin
                a_json = JSON.parse(rs)
                return PhalApi::ClientResponse.new(a_json['ret'], a_json['data'], a_json['msg'])
            rescue JSON::ParserError => e
                return PhalApi::ClientResponse.new(500, [], 'Internal Server Error')
            end
        end
    end

    # 接口过滤器
    class ClientFilter
        def filter(service, *params)
            #nothing here ...
        end
    end

end
