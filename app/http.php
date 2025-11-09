<?php

require_once __DIR__.'/../vendor/autoload.php';

use HTTPGames\Module;
use HTTPGames\Platform;
use Utopia\App;
use Utopia\Platform\Service;
use Utopia\Request;
use Utopia\Response;

// Endpoints
$module = new Module;
$platform = new Platform($module);
$platform->init(Service::TYPE_HTTP);

// Server
$app = new App('UTC');
$app->run(new Request, new Response);
