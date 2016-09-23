module PhalApi
    class Client
        # 接口返回结果
        # 
        # - 与接口返回的格式对应，即有：ret/data/msg
        class Response
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

            def to_s
                "[PhalApi::Client::Response]\n" +
                "Ret:       #{@ret}\n" +
                "Data:      #{@data}\n" +
                "Msg:       #{@msg}\n"
            end

            def to_str
                to_s
            end
        end
    end
end

