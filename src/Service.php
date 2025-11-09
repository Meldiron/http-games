<?php

namespace HTTPGames;

use HTTPGames\Games\GridTrap\Create as CreateGridTrapGame;
use HTTPGames\Health\Get as GetHealth;
use HTTPGames\Hooks\OnError;
use HTTPGames\Hooks\OnInit;
use HTTPGames\Hooks\OnShutdown;
use HTTPGames\Users\Create as CreateUser;
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

        $this->addAction('getHealth', new GetHealth);

        $this->addAction('createUser', new CreateUser);
    }
}
