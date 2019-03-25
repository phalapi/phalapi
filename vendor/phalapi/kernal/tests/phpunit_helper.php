<?php

// 兼容旧版本的PHPUnit
if (!class_exists('PHPUnit_Framework_TestCase')) {
    class PHPUnit_Framework_TestCase extends PHPUnit\FrameWork\TestCase {
    }
}
