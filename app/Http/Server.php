<?php

namespace App\Http;

class Server
{
    public static function get(string $key)
    {
        return (isset($_SERVER[$key]) ? $_SERVER[$key] : null);
    }
}