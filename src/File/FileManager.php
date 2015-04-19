<?php

namespace FershoPls\File;

class FileManager {

    protected $absolute;

    public function __construct ($absolute = __DIR__)
    {
        $this->absolute = $absolute;
    }

    public function fallback ($relative_route)
    {
        // If the route ends with a file .php, .txt, .jpg; Remove it;
        $relative_route = preg_replace("/\/([a-z0-9-_]+\.[a-z0-9]+)?$/", DIRECTORY_SEPARATOR, $relative_route);
        // Make a route
        $relative_route = $this->getRoute($relative_route);

        if (!$this->exists($relative_route))
        {
            mkdir($relative_route, 0777, true);
        }

    }

    public function exists ($route)
    {
        $route = $this->getRoute($route);
        return file_exists($route);
    }

    public function load ($route = null, $fallback = false)
    {
        $route = $this->getRoute($route);
        if ($this->exists($route))
        {
            return file_get_contents($route);
        }
        return $fallback;
    }

    public function write ($route = null, $content = "", $append = false)
    {
        $route = $this->getRoute($route);
        if ($append)
        {
            file_put_contents($route, $content, FILE_APPEND);
        } else {
            file_put_contents($route, $content);
        }
    }

    public function forDirectory ($route, $function)
    {
        $route = $this->getRoute($route);

        if (!$this->exists($route))
        {
            echo ("Couldn't find directory {$route}." . PHP_EOL);
        }

        $route_items = scandir($route);
        // Remove `.` and `..`
        unset($route_items[0]);unset($route_items[1]);

        foreach ($route_items as $item) {
            $item_route = $this->getRoute($this->join($route, $item));
            $function($item_route);
        }
    }

    protected function getRoute ($route = null)
    {
        // We will work with normal slash always `/`
        $route_requested = $this->replaceDirectorySeparator($route);
        $route_absolute  = $this->replaceDirectorySeparator(realpath($this->absolute));

        // If it is absolute route
        if (!preg_match("/^\/{2}/", $route_requested))
            $route_requested = preg_replace("/^\/(.+)/", $route_absolute . "/$1", $route_requested);

        // Remove last slash `/`
        $route_requested = preg_replace("/(.+)\/$/", "$1", $route_requested);

        // Return to system slash `DIRECTORY_SEPARATOR`
        $route_requested = $this->replaceDirectorySeparator($route_requested, DIRECTORY_SEPARATOR);

        // Return
        return $route_requested;
    }

    protected function replaceDirectorySeparator ($route = "", $character = "/")
    {
        return str_replace(["\\", "/", "//", "\\\\"], $character, $route);
    }

    public function join ($piece1 = null, $piece2 = null)
    {
        if ($piece1 && $piece2)
        {
            $piece1 = $this->getRoute($piece1);
            $piece2 = $this->getRoute($piece2);
            return $piece1 . DIRECTORY_SEPARATOR . $piece2;
        }
    }

}
