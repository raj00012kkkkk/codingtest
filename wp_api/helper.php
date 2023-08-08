<?php

class Helper
{
    protected $route;
    public function check($route)
    {
        $this->route = $route;
        $method = $_SERVER["REQUEST_METHOD"];

        return self::methodcheck($this->route, $method);
    }

    protected static function methodcheck($route, $method)
    {
        if (isset($_REQUEST['page'])) {
            $reqpage = $_REQUEST['page'];
        } else {
            $reqpage = 1;
        }


        if ($route == "pages/{$reqpage}" && $method == "GET") {
            return true;
        } elseif ($route == "page/update" && $method == "POST") {
            return true;
        } elseif ($route == "page" && $method == "GET") {
            return true;
        } else {
            return false;
        }
    }
}
