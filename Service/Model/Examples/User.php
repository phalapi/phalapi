<?php

class Model_Examples_User
{
    public function getByUserId($userId)
    {
        return DI()->notorm->user->select('*')->where('id = ?', $userId)->fetch();
    }
}
