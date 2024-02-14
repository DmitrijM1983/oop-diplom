<?php

namespace Repository;

class Connection
{
    public static function getConnect()
    {
        return new \PDO('mysql:host=127.0.0.1;dbname=marlin;charset=utf8', 'root', '');
    }
}