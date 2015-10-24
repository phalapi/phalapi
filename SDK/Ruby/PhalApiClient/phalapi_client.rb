require 'open-uri'
require 'net/http'
require 'json'


module PhalApi
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

        def request
            url = @host

            url += "?service=" + @service if @service != nil

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

    class ClientParser
        def parse(rs)
            raise 'hey guys, you should rewrite PhalApi::ClientPaser.parse'
        end
    end

    class ClientParserJson < PhalApi::ClientParser
        def parse(rs)
            return PhalApi::ClientResponse.new(408, [], 'Request Timeout') if rs == nil

            a_json = JSON.parse(rs)

            return PhalApi::ClientResponse.new(500, [], 'Internal Server Error') if a_json == nil

            return PhalApi::ClientResponse.new(a_json['ret'], a_json['data'], a_json['msg'])
        end
    end

    class ClientFilter
        def filter(service, *params)
            #nothing here ...
            #params.each do |key, value| puts "filter: #{key}, #{value}" end
        end
    end

end
