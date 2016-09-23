module PhalApi
    class Client
        # 接口结果解析器
        # 
        # - 可用于不同接口返回格式的处理
        class Parser
            def parse(rs)
                raise 'Hey guys, you should rewrite PhalApi::ClientPaser.parse'
            end
        end
    end
end

