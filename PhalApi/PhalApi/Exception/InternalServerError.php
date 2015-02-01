<?php

class PhalApi_Exception_InternalServerError extends PhalApi_Exception
{
    public function __construct($message, $code = 0)
    {
        parent::__construct(T('Interal Server Error: {message}', array('message' => $message)), 500 + $code);
    }
}
