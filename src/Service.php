<?php

namespace HTTPGames;

use HTTPGames\Events\OnError;
use HTTPGames\Events\OnInit;
use HTTPGames\Events\OnShutdown;
use HTTPGames\Games\GridTrap\Create as CreateGridTrapGame;
use Utopia\Platform\Service as UtopiaService;

class Service extends UtopiaService
{
    public function __construct()
    {
        $this->type = Service::TYPE_HTTP;

        $this->addAction('onError', new OnError);
        $this->addAction('onInit', new OnInit);
        $this->addAction('onShutdown', new OnShutdown);

        $this->addAction('createGridTrapGame', new CreateGridTrapGame);
    }
}
