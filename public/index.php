<?php

require __DIR__."/../vendor/autoload.php";

use Symfony\Component\HttpFoundation\Request;
use App\Kernel;

$request = Request::createFromGlobals();

$kernel = new Kernel();
$kernel->handle($request);
