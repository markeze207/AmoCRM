<?php
require_once(dirname(__DIR__,1).'/vendor/autoload.php');
require_once(dirname(__DIR__,1).'/routes/web.php');

use App\RMVC\App;

App::run();