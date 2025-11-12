<?php

use Appwrite\AppwriteException;
use Appwrite\Services\TablesDB;
use HTTPGames\Migration\Migration;
use Utopia\Database\Query;

function setupSchema(TablesDB $sdkForTables, string $databaseId): void
{
    try {
        $sdkForTables->getTable($databaseId, 'ready001');
    } catch (AppwriteException $err) {
        if ($err->getType() !== 'database_not_found' && $err->getType() !== 'table_not_found' && $err->getCode() !== 500) {
            throw $err;
        }

        // Upsert database
        $exists = false;
        try {
            // TODO: Serverless function for cleanup every 7 days
            $sdkForTables->create(
                databaseId: $databaseId,
                name: $databaseId,
            );
        } catch (AppwriteException $err) {
            if ($err->getType() === 'database_already_exists') {
                $exists = true;
            } else {
                throw $err;
            }
        }

        // Setup all tables
        if (! $exists) {
            $migration = new Migration($sdkForTables, $databaseId);
            $migration->apply();

            $sdkForTables->createTable($databaseId, 'ready000', '_ready000');
        }

        // Wait until last table is ready
        $attempt = 0;
        while (true) {
            $attempt++;
            if ($attempt > 15) {
                throw new Exception('Failed to unlock tables.');
            }

            try {
                $sdkForTables->getTable($databaseId, 'ready000');
                break;
            } catch (AppwriteException $err) {
                if ($err->getType() === 'table_not_found' || $err->getCode() === 500) {
                    \sleep(1);

                    continue;
                }

                throw $err;
            }
        }

        // Ensure all tables, attributes, and indexes are ready
        $tables = $sdkForTables->listTables($databaseId, [
            Query::limit(500),
        ]);
        $tableIds = \array_column($tables['tables'], '$id');
        $attempts = 0;
        while (true) {
            $processing = false;
            foreach ($tableIds as $tableId) {
                // TODO: Switch to status query once Appwrite supports it
                $columns = $sdkForTables->listColumns(
                    $databaseId,
                    $tableId,
                    [
                        // Query::notEqual('status', 'available'),
                        // Query::limit(1),
                        Query::limit(100),
                    ],
                );

                foreach ($columns['columns'] as $column) {
                    if ($column['status'] !== 'available') {
                        $processing = true;
                        break;
                    }
                }

                if ($processing) {
                    break;
                }

                /*
                if ($rows['total'] > 0) {
                    $processing = true;
                }
                */
            }

            if (! $processing) {
                break;
            }

            $attempts++;
            if ($attempts > 15) {
                throw new Exception('Database not setup properly.');
            }

            \sleep(1);
        }

        // Optimize future DB readyness check
        try {
            $sdkForTables->createTable($databaseId, 'ready001', '_ready001');
        } catch (AppwriteException $err) {
            if ($err->getType() === 'table_already_exists') {
                // OK
            } else {
                throw $err;
            }
        }

    }
}
