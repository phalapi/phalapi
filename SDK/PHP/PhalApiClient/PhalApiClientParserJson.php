<?php

class PhalApiClientParserJson implements PhalApiClientParser {

    public function parse($apiResult) {
        $arr = json_decode($apiResult, true);

        if ($arr === false || empty($arr)) {
            return new PhalApiClientResponse(500);
        }

        return new PhalApiClientResponse($arr['ret'], $arr['data'], $arr['msg']);
    }
}
