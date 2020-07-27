<?php

use Composer\Autoload\ClassLoader;

$loader = new ClassLoader();
$loader->addPsr4('App\\',[dirname(__DIR__)]);
$loader->register(true);
