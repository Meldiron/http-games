<?php

namespace HTTPGames;

use HTTPGames\Games\GridTrap\Dungeons\Create as CreateGridTrapDungeon;
use HTTPGames\Games\GridTrap\Dungeons\Get as GetGridTrapDungeon;
use HTTPGames\Games\GridTrap\Dungeons\XList as ListGridTrapDungeon;
use HTTPGames\Health\Get as GetHealth;
use HTTPGames\Hooks\OnError;
use HTTPGames\Hooks\OnInit;
use HTTPGames\Hooks\OnInitWithSession;
use HTTPGames\Hooks\OnShutdown;
use HTTPGames\Tokens\Create as CreateToken;
use HTTPGames\Users\Create as CreateUser;
use HTTPGames\Users\Get as GetUser;
use Utopia\Platform\Service as UtopiaService;

class Service extends UtopiaService
{
    public function __construct()
    {
        $this->type = Service::TYPE_HTTP;

        // Hooks

        $this->addAction('onError', new OnError);
        $this->addAction('onInit', new OnInit);
        $this->addAction('onShutdown', new OnShutdown);

        $this->addAction('onInitWithSession', new OnInitWithSession);

        // Endpoints

        $this->addAction('getHealth', new GetHealth);

        $this->addAction('createUser', new CreateUser);
        $this->addAction('createToken', new CreateToken);
        $this->addAction('getUser', new GetUser);

        // TODO: listGames
        // TODO: getGame

        $this->addAction('createGridTrapDungeon', new CreateGridTrapDungeon);
        $this->addAction('getGridTrapDungeon', new GetGridTrapDungeon);
        $this->addAction('listGridTrapDungeon', new ListGridTrapDungeon);
    }
}
