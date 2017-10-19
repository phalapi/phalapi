<?php
/**
 * PhalApi 2.x 与 PhalApi 1.x 的映射关系
 *
 * 如果需要进行兼容，可以加载此文件
 *
 * @author dogstar<chanzonghuang@gmail.com> 2017-07-16
 */

class PhalApi extends PhalApi\PhalApi {};
class PhalApi_Api extends PhalApi\Api {};
class PhalApi_ApiFactory extends PhalApi\ApiFactory {};
interface PhalApi_Cache extends PhalApi\Cache {};
class PhalApi_Cache_APCU extends PhalApi\Cache\APCUCache {};
class PhalApi_Cache_File extends PhalApi\Cache\FileCache {};
class PhalApi_Cache_Memcache extends PhalApi\Cache\MemcacheCache {};
class PhalApi_Cache_Memcached extends PhalApi\Cache\MemcachedCache {};
class PhalApi_Cache_Multi extends PhalApi\Cache\MultiCache {};
class PhalApi_Cache_None extends PhalApi\Cache\NoneCache {};
class PhalApi_Cache_Redis extends PhalApi\Cache\RedisCache {};
interface PhalApi_Config extends PhalApi\Config {};
class PhalApi_Config_File extends PhalApi\Config\FileConfig {};
class PhalApi_Config_Yaconf extends PhalApi\Config\YaconfConfig {};
class PhalApi_Cookie extends PhalApi\Cookie {};
class PhalApi_Cookie_Multi extends PhalApi\Cookie\MultiCookie {};
interface PhalApi_Crypt extends PhalApi\Crypt {};
class PhalApi_Crypt_Mcrypt extends PhalApi\Crypt\McryptCrypt {};
class PhalApi_Crypt_MultiMcrypt extends PhalApi\Crypt\MultiMcryptCrypt {};
class PhalApi_Crypt_RSA_KeyGenerator extends PhalApi\Crypt\RSA\KeyGenerator {};
abstract class PhalApi_Crypt_RSA_MultiBase extends PhalApi\Crypt\RSA\MultiBase {};
class PhalApi_Crypt_RSA_MultiPri2Pub extends PhalApi\Crypt\RSA\MultiPri2PubCrypt {};
class PhalApi_Crypt_RSA_MultiPub2Pri extends PhalApi\Crypt\RSA\MultiPub2PriCrypt {};
class PhalApi_Crypt_RSA_Pri2Pub extends PhalApi\Crypt\RSA\Pri2PubCrypt {};
class PhalApi_Crypt_RSA_Pub2Pri extends PhalApi\Crypt\RSA\Pub2PriCrypt {};
class PhalApi_CUrl extends PhalApi\CUrl {};
interface PhalApi_DB extends PhalApi\Database {};
class PhalApi_DB_NotORM extends PhalApi\Database\NotORMDatabase {};
class PhalApi_DI extends PhalApi\DependenceInjection {};
class PhalApi_Exception extends PhalApi\Exception {};
class PhalApi_Exception_BadRequest extends PhalApi\Exception\BadRequestException {};
class PhalApi_Exception_InternalServerError extends PhalApi\Exception\InternalServerErrorException {};
class PhalApi_Exception_Redirect extends PhalApi\Exception\RedirectException {};
interface PhalApi_Filter extends PhalApi\Filter {};
class PhalApi_Filter_None extends PhalApi\Filter\NoneFilter {};
class PhalApi_Filter_SimpleMD5 extends PhalApi\Filter\SimpleMD5Filter {};
class PhalApi_Helper_ApiDesc extends PhalApi\Helper\ApiDesc {};
class PhalApi_Helper_ApiList extends PhalApi\Helper\ApiList {};
class PhalApi_Helper_ApiOnline extends PhalApi\Helper\ApiOnline {};
class PhalApi_Helper_TestRunner extends PhalApi\Helper\TestRunner {};
class PhalApi_Helper_Tracer extends PhalApi\Helper\Tracer {};
class PhalApi_Loader extends PhalApi\Loader {};
abstract class PhalApi_Logger extends PhalApi\Logger {};
class PhalApi_Logger_Explorer extends PhalApi\Logger\ExplorerLogger {};
class PhalApi_Logger_File extends PhalApi\Logger\FileLogger {};
interface PhalApi_Model extends PhalApi\Model {};
class PhalApi_Model_NotORM extends PhalApi\Model\NotORMModel {};
abstract class PhalApi_ModelProxy extends PhalApi\Model\Proxy {};
class PhalApi_ModelQuery extends PhalApi\Model\Query {};
class PhalApi_Request extends PhalApi\Request {};
interface PhalApi_Request_Formatter extends PhalApi\Request\Formatter {};
class PhalApi_Request_Formatter_Array extends PhalApi\Request\Formatter\ArrayFormatter {};
class PhalApi_Request_Formatter_Base extends PhalApi\Request\Formatter\BaseFormatter {};
class PhalApi_Request_Formatter_Boolean extends PhalApi\Request\Formatter\BooleanFormatter {};
class PhalApi_Request_Formatter_Callable extends PhalApi\Request\Formatter\CallableFormatter {};
class PhalApi_Request_Formatter_Callback extends PhalApi\Request\Formatter\CallbackFormatter {};
class PhalApi_Request_Formatter_Date extends PhalApi\Request\Formatter\DateFormatter {};
class PhalApi_Request_Formatter_Enum extends PhalApi\Request\Formatter\EnumFormatter {};
class PhalApi_Request_Formatter_File extends PhalApi\Request\Formatter\FileFormatter {};
class PhalApi_Request_Formatter_Float extends PhalApi\Request\Formatter\FloatFormatter {};
class PhalApi_Request_Formatter_Int extends PhalApi\Request\Formatter\IntFormatter {};
class PhalApi_Request_Formatter_String extends PhalApi\Request\Formatter\StringFormatter {};
class PhalApi_Request_Var extends PhalApi\Request\Parser {};
abstract class PhalApi_Response extends PhalApi\Response {};
class PhalApi_Response_Explorer extends PhalApi\Response\ExplorerResponse {};
class PhalApi_Response_Json extends PhalApi\Response\JsonResponse {};
class PhalApi_Response_JsonP extends PhalApi\Response\JsonpResponse {};
class PhalApi_Response_Xml extends PhalApi\Response\XmlResponse {};
class PhalApi_Tool extends PhalApi\Tool {};
class PhalApi_Translator extends PhalApi\Translator {};

function DI() {
    return PhalApi\DI();
}

function SL($language) {
    return PhalApi\SL($language);
}

function T($msg, $params = array()) {
    return PhalApi\T($msg, $params);
}

