<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSchemaService
{
    /**
     * List of sensitive columns that must NEVER be exposed to the AI schema.
     */
    protected array $hiddenColumns = [
        'password',
        'remember_token',
        'api_key',
        'secret',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * List of internal tables to exclude from AI analysis unless relevant.
     */
    protected array $excludedTables = [
        'migrations',
        'failed_jobs',
        'personal_access_tokens',
        'password_reset_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'ai_conversations',
        'ai_messages',
        'ai_settings',
    ];

    /**
     * Filter table names to only include tables from current database.
     */
    public function getCleanTables(): array
    {
        $rawTables = Schema::getTableListing();
        $currentDb = DB::connection()->getDatabaseName();
        $cleanTables = [];

        foreach ($rawTables as $t) {
            $tableName = $t;
            if (strpos($t, '.') !== false) {
                $parts = explode('.', $t);
                $dbName = $parts[0];
                $tableName = $parts[1];

                // Exclude tables belonging to other databases on same MySQL instance
                if (strtolower($dbName) !== strtolower($currentDb)) {
                    continue;
                }
            }

            if (in_array($tableName, $this->excludedTables)) {
                continue;
            }

            $cleanTables[] = $tableName;
        }

        return array_unique($cleanTables);
    }

    /**
     * Generate a clean Markdown description of all application database tables.
     */
    public function getSchemaSummary(): string
    {
        $tables = $this->getCleanTables();
        $schemaText = "# DATABASE SCHEMA & STRUCTURE\n\n";

        foreach ($tables as $table) {
            $schemaText .= "### Table: `{$table}`\n";
            $columns = Schema::getColumnListing($table);

            $schemaText .= "Columns: ";
            $visibleColumns = [];

            foreach ($columns as $column) {
                if (in_array(strtolower($column), $this->hiddenColumns)) {
                    continue;
                }
                $type = Schema::getColumnType($table, $column);
                $visibleColumns[] = "`{$column}` ({$type})";
            }

            $schemaText .= implode(', ', $visibleColumns) . "\n\n";
        }

        return $schemaText;
    }
}
