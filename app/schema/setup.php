<?php

use Appwrite\AppwriteException;
use Appwrite\Services\TablesDB;
use Utopia\Database\Document;
use Utopia\Database\Query;

require_once __DIR__.'/tables/grid-trap.php';
require_once __DIR__.'/tables/tokens.php';
require_once __DIR__.'/tables/users.php';


function setupSchema(TablesDB $sdkForTables, string $databaseId): void
{
    try {
        // TODO: Optimize for performance on production, those calls should not be needed
        $sdkForTables->get($databaseId);
        $sdkForTables->getTable($databaseId, 'ready001');
    } catch (AppwriteException $err) {
        if ($err->getType() !== 'database_not_found' && $err->getType() !== 'table_not_found' && $err->getCode() !== 500) {
            throw $err;
        }

        // Upsert database
        $exists = false;
        try {
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

        /**
         * @var array<Document> $tables
         */
        $tables = [];

        // Setup all tables
        if (! $exists) {
            $tables = \array_merge(
                $tables,
                setupUsers($sdkForTables, $databaseId),
                setupTokens($sdkForTables, $databaseId),
                setupGridTrap($sdkForTables, $databaseId)
            );

            // Always keep last
            $sdkForTables->createTable($databaseId, 'ready000', 'Tables are ready');
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
        $attempts = 0;
        while (true) {
            $processing = false;
            foreach ($tables as $table) {
                try {
                    $rows = $sdkForTables->listColumns(
                        $databaseId,
                        $table->getId(),
                        [
                            Query::notEqual('status', 'available'),
                            Query::limit(1),
                        ],
                    );

                    if ($rows['total'] > 0) {
                        $processing = true;
                    }
                } catch (AppwriteException $err) {
                    if ($err->getType() === 'table_not_found' || $err->getCode() === 500) {
                        $processing = true;
                        break;
                    }

                    throw $err;
                }
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
            $sdkForTables->createTable($databaseId, 'ready001', 'Columns are ready');
        } catch (AppwriteException $err) {
            if ($err->getType() === 'table_already_exists') {
                // OK
            } else {
                throw $err;
            }
        }

    }
}
