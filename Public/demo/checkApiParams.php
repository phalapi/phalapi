<?php
/**
 * PhalApi Online API Detail Document (auto generated)
 * 
 * - check API description
 * - check API params rules
 * - check API reponse
 * - check API exception case
 */

require_once dirname(__FILE__) . '/../init.php';

/**
 * TODO: Lad your API folder
 */
DI()->loader->addDirs('Demo');

/**
 * Library
 *
 * TODO: Add the path of libraries, such as ```Library/XXX/XXX```
 */
$libraryPaths = array(
    'Library/User/User',    // User Library
    'Library/Auth/Auth',    // Auth Library
);

foreach ($libraryPaths as $aPath) {
    $toAddDir = str_replace('/', DIRECTORY_SEPARATOR, $aPath);
    DI()->loader->addDirs($toAddDir);
}

$apiDesc = new PhalApi_Helper_ApiDesc();
$apiDesc->render();

