<?php

require_once dirname(__FILE__) . '/../../test_env.php';

$str = "人这一辈子，无非就是个过程，荣华花间露，富贵草上霜，生不带来，死不带去，得意些什么？失意些什么？顺其自然、随遇而安，如行云般自在，像流水般洒脱，才是人生应有的态度。";

$coreMultiCryptMcrypt = new Core_Crypt_MultiMcrypt('12345678');

$key = 'dogstar';

for ($i = 0; $i < $argv[1]; $i ++) {
    $coreMultiCryptMcrypt->decrypt($coreMultiCryptMcrypt->encrypt($str, $key), $key);
}

