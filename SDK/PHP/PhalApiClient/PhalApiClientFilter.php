<?php

interface PhalApiClientFilter {

    public function filter($service, array &$params);
}
