<?php

namespace App\Http\Router;

use App\Http\Server;

class Router
{
    public static function get($route, $callback)
    {
        if (strcasecmp(Server::get('REQUEST_METHOD'), 'GET') !== 0) {
            return;
        }

        self::on($route, $callback);
    }

    public static function post($route, $callback)
    {
        if (strcasecmp(Server::get('REQUEST_METHOD'), 'POST') !== 0) {
            return;
        }

        self::on($route, $callback);
    }

    public static function on($regex, $callback)
    {
        $params = Server::get('REQUEST_URI');
        $params = (stripos($params, "/") !== 0) ? "/" . $params : $params;
        $regex = str_replace('/', '\/', $regex);
        $isMatch = preg_match('/^' . ($regex) . '$/', $params, $matches, PREG_OFFSET_CAPTURE);

        if ($isMatch) {
            // first value is normally the route, lets remove it
            array_shift($matches);
            // Get the matches as parameters
            $params = array_map(function ($param) {
                return $param[0];
            }, $matches);
            $callback(new Request($params), new Response());
        }
    }
}
