<?php

require_once (
    realpath(__DIR__ . '/vendor/autoload.php')
);

$file = new FershoPls\File\FileManager(__DIR__);

echo $file->join('/vendor/', 'autoload.php');