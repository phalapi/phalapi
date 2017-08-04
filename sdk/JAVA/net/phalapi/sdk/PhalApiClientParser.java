package net.phalapi.sdk;

/**
 * 接口结果解析器
 * 
 * - 可用于不同接口返回格式的处理
 */
public interface PhalApiClientParser {

    /**
     * 结果解析
     * @param String apiResult
     * @return PhalApiClientResponse
     */
    public PhalApiClientResponse parse(String apiResult);
}

