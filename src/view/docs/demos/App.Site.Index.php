$rs = $client->reset()
    ->withService('{s}')
    ->withParams('username', 'PhalApi')
    ->withTimeout(3000)
    ->request();