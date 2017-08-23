<?php

require_once dirname(__FILE__) . '/../src/Lite.php';

class PHPUnderControll_Lite_Test extends PHPUnit_FrameWork_TestCase {

    public function testHere() {
        $pdo = new PDO('mysql:dbname=phalapi;host=localhost;port=3306', 'root', '123'); 

        $lite = new PhalApi\NotORM\Lite($pdo);

        $structure = new NotORM_Structure_Convention('key', '%s_id', '%s', 'prefix');
        $lite->setStructure($structure);

    }
}
