<?php

namespace HTTPGames\Migration\Schemas;

use HTTPGames\Migration\Schema;

class Tokens extends Schema
{
    public function apply(): void
    {
        $this->sdkForTables->createTable($this->databaseId, 'tokens', 'Tokens');
        $this->sdkForTables->createStringColumn(
            $this->databaseId,
            'tokens',
            'userId',
            255,
            required: true,
        );
        $this->sdkForTables->createStringColumn(
            $this->databaseId,
            'tokens',
            'secret',
            255,
            required: true,
        );
    }
}
