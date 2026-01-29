<?php
header('Content-Type: text/markdown; charset=utf-8');
header('Content-Disposition:  attachment; filename="' . $service . '.md"');  

$apiHost = $_SERVER['HTTP_HOST'] ?? '你的接口域名';
$md = \App\Common\ApiDoc::generateMarkdown($projectName, $apiHost, $service, $methods, $version, $description, $descComment, $rules, $returns, $exceptions);

echo $md;

