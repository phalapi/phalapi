<?php
namespace PhalApi\Tests;

use PhalApi\Request\Formatter\FileFormatter;

/**
 * PhpUnderControl_PhalApiRequestFormatterFile_Test
 *
 * 针对 ../../../PhalApi/Request/Formatter/File.php PhalApi_Request_Formatter_File 类的PHPUnit单元测试
 *
 * @author: dogstar 20160101
 */

class PhpUnderControl_PhalApiRequestFormatterFile_Test extends \PHPUnit_Framework_TestCase
{
    public $fileFormatter;

    protected function setUp()
    {
        parent::setUp();

        $this->fileFormatter = new FileFormatter();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testParse
     */ 
    public function testParse()
    {
        $value = array();

        $_FILES['aFile'] = array('name' => 'aHa~', 'type' => 'image/jpeg', 'size' => 100, 'tmp_name' => '/tmp/123456', 'error' => 0);

        $rule = array('name' => 'aFile', 'range' => array('image/jpeg'), 'min' => 50, 'max' => 1024, 'require' => true, 'default' => array(), 'type' => 'file');

        $rs = $this->fileFormatter->parse($value, $rule);
    }

    /**
     * @dataProvider provideFileForSuffix
     */
    public function testSuffixSingleInArray($fileIndex, $fileData)
    {
        $_FILES[$fileIndex] = $fileData;
        $value = array();

        $rule = array(
            'name' => $fileIndex, 
            'require' => true, 
            'default' => array(), 
            'ext' => array('txt'),
            'type' => 'file',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
        $this->assertEquals($fileData, $rs);
    }

    /**
     * @dataProvider provideFileForSuffix
     */
    public function testSuffixSingleInString($fileIndex, $fileData)
    {
        $_FILES[$fileIndex] = $fileData;
        $value = array();

        $rule = array(
            'name' => $fileIndex, 
            'require' => true, 
            'default' => array(), 
            'ext' => 'txt',
            'type' => 'file',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
        $this->assertEquals($fileData, $rs);
    }

    /**
     * @dataProvider provideFileForSuffix
     */
    public function testSuffixMultiInArray($fileIndex, $fileData)
    {
        $_FILES[$fileIndex] = $fileData;
        $value = array();

        $rule = array(
            'name' => $fileIndex, 
            'require' => true, 
            'default' => array(), 
            'ext' => array('TXT', 'dat', 'bak'),
            'type' => 'file',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
        $this->assertEquals($fileData, $rs);
    }

    /**
     * @dataProvider provideFileForSuffix
     */
    public function testSuffixSingleInMulti($fileIndex, $fileData)
    {
        $_FILES[$fileIndex] = $fileData;
        $value = array();

        $rule = array(
            'name' => $fileIndex, 
            'require' => true, 
            'default' => array(), 
            'ext' => 'DAT, txt, baK',
            'type' => 'file',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
        $this->assertEquals($fileData, $rs);
    }

    /**
     * @expectedException PhalApi\Exception\BadRequestException
     */
    public function testSuffixForSpecialBug()
    {
        // no ext
        $aFile = array(
            'name' => '2016', 
            'type' => 'application/text', 
            'size' => 100, 
            'tmp_name' => '/tmp/123456', 
            'error' => 0
        );
        $_FILES['aFile'] = $aFile;
        $value = array();

        $rule = array(
            'name' => 'aFile', 
            'require' => true, 
            'default' => array(), 
            'ext' => 'txt, DAT, baK,', //小心最后的逗号
            'type' => 'file',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
    }

    /**
     * @expectedException PhalApi\Exception\BadRequestException
     * @expectedExceptionMessage 兼容特别的bug时的错误提示
     */
    public function testSuffixForSpecialBugWithMessage()
    {
        // no ext
        $aFile = array(
            'name' => '2016', 
            'type' => 'application/text', 
            'size' => 100, 
            'tmp_name' => '/tmp/123456', 
            'error' => 0
        );
        $_FILES['aFile'] = $aFile;
        $value = array();

        $rule = array(
            'name' => 'aFile', 
            'require' => true, 
            'default' => array(), 
            'ext' => 'txt, DAT, baK,', //小心最后的逗号
            'type' => 'file',
            'message' => '兼容特别的bug时的错误提示',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
    }

    /**
     * @dataProvider provideFileForSuffix
     * @expectedException PhalApi\Exception\BadRequestException
     */
    public function testSuffixMultiInArrayAndExcpetion($fileIndex, $fileData)
    {
        $_FILES[$fileIndex] = $fileData;
        $value = array();

        $rule = array(
            'name' => $fileIndex, 
            'require' => true, 
            'default' => array(), 
            'ext' => array('XML', 'HTML'),
            'type' => 'file',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
    }

    /**
     * @dataProvider provideFileForSuffix
     * @expectedException PhalApi\Exception\BadRequestException
     * @expectedExceptionMessage 数组格式的扩展名错误提示
     */
    public function testSuffixMultiInArrayAndExcpetionWithMessage($fileIndex, $fileData)
    {
        $_FILES[$fileIndex] = $fileData;
        $value = array();

        $rule = array(
            'name' => $fileIndex, 
            'require' => true, 
            'default' => array(), 
            'ext' => array('XML', 'HTML'),
            'type' => 'file',
            'message' => '数组格式的扩展名错误提示',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
    }

    public function provideFileForSuffix()
    {
        // one ext
        $bFile = array(
            'name' => '2016.txt', 
            'type' => 'application/text', 
            'size' => 100, 
            'tmp_name' => '/tmp/123456', 
            'error' => 0
        );
        // tow ext
        $cFile = array(
            'name' => '2016.log.txt', 
            'type' => 'application/text', 
            'size' => 100, 
            'tmp_name' => '/tmp/123456', 
            'error' => 0
        );

        return array(
            array('bFile', $bFile),
            array('cFile', $cFile),
        );
    }

    public function testParseNotRequire()
    {
        $value = array();

        $rule = array(
            'name' => 'maybeFile', 
            'require' => false, 
            'type' => 'file',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
        $this->assertNull($rs);
    }

    public function testParseNotRequireButUpload()
    {
        $_FILES['maybeFile'] = array(
            'name' => '2016.log.txt', 
            'type' => 'application/text', 
            'size' => 100, 
            'tmp_name' => '/tmp/123456', 
            'error' => 0
        );
        $value = array();

        $rule = array(
            'name' => 'maybeFile', 
            'require' => false, 
            'type' => 'file',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
        $this->assertNotNull($rs);
    }

    /**
     * @expectedException PhalApi\Exception\BadRequestException
     */
    public function testUploadNothing()
    {
        $_FILES = array();
        $value = array();

        $rule = array(
            'name' => 'maybeFile', 
            'require' => true, 
            'type' => 'file',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
    }

    /**
     * @expectedException PhalApi\Exception\BadRequestException
     * @expectedExceptionMessage 缺少上传文件
     */
    public function testUploadNothingWithMessage()
    {
        $_FILES = array();
        $value = array();

        $rule = array(
            'name' => 'maybeFile', 
            'require' => true, 
            'type' => 'file',
            'message' => '缺少上传文件',
        );
        $rs = $this->fileFormatter->parse($value, $rule);
    }
}
