<?php

namespace HTTPGames\Migration\Schemas;

use HTTPGames\Migration\Schema;

class Users extends Schema
{
    public function apply(): void
    {
        $this->sdkForTables->createTable($this->databaseId, 'users', 'Users');
        $this->sdkForTables->createStringColumn(
            $this->databaseId,
            'users',
            'email',
            255,
            required: true,
        );
        $this->sdkForTables->createStringColumn(
            $this->databaseId,
            'users',
            'passwordHash',
            255,
            required: true,
            encrypt: true,
        );
        $this->sdkForTables->createStringColumn(
            $this->databaseId,
            'users',
            'nickname',
            255,
            required: true,
        );
    }
}
