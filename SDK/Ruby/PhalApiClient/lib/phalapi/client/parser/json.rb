require "phalapi/client/parser"
require "phalapi/client/response"

module PhalApi
    class Client
        # JSON解析
        class Parser
            class Json < PhalApi::Client::Parser
                def parse(rs)
                    return PhalApi::Client::Response.new(408, [], 'Request Timeout') if rs == nil

                    begin
                        a_json = JSON.parse(rs)
                        return PhalApi::Client::Response.new(a_json['ret'], a_json['data'], a_json['msg'])
                    rescue JSON::ParserError => e
                        return PhalApi::Client::Response.new(500, [], 'Internal Server Error')
                    end
                end
            end
        end
    end
end

