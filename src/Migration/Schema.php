<?php

namespace HTTPGames\Migration;

use Appwrite\Services\TablesDB;

abstract class Schema
{
    abstract public function apply(): void;

    public function __construct(protected TablesDB $sdkForTables, protected string $databaseId) {}
}
