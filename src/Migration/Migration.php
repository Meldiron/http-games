<?php

namespace HTTPGames\Migration;

use Appwrite\Services\TablesDB;
use HTTPGames\Migration\Schemas\GridTrap;
use HTTPGames\Migration\Schemas\Tokens;
use HTTPGames\Migration\Schemas\Users;

class Migration
{
    /**
     * @var array<class-string<Schema>>
     */
    protected $schemas;

    public function __construct(protected TablesDB $sdkForTables, protected string $databaseId)
    {
        $this->schemas = [
            Users::class,
            Tokens::class,
            GridTrap::class,
        ];
    }

    public function apply(): void
    {
        foreach ($this->schemas as $schemaClass) {
            $schema = new $schemaClass($this->sdkForTables, $this->databaseId);
            $schema->apply();
        }
    }
}
