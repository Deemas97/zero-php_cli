#!/usr/bin/php
<?php

namespace Application;

include_once './src/Kernel.php';

use App\Kernel;

$appCore = new Kernel();
$appCore->run();
