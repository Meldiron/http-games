<?php

namespace HTTPGames;

use Utopia\Platform\Module;
use Utopia\Platform\Platform as UtopiaPlatform;

class Platform extends UtopiaPlatform
{
    public function __construct(Module $module)
    {
        parent::__construct($module);

        $this->addService('http', new Service);
    }
}
