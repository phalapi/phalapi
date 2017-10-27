<?php
namespace PhalApi\Helper;

class ApiOnline {

    protected $projectName;

    public function __construct($projectName) {
        $this->projectName = $projectName;
    }

    public function render() {
        header('Content-Type:text/html;charset=utf-8');
    }
}
