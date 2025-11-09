<?php

require_once __DIR__.'/../vendor/autoload.php';

use HTTPGames\Module;
use HTTPGames\Platform;
use Utopia\Platform\Service;

$module = new Module;
$platform = new Platform($module);
$platform->init(Service::TYPE_HTTP);
