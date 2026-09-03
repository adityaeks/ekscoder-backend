<?php

namespace App\Services;

use App\Models\User;
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
     * Mapping of database tables to their required Spatie permissions.
     */
    public const TABLE_PERMISSION_MAP = [
        'financial_transactions' => 'finance.view',
        'financial_categories'   => 'finance.view',
        'project_orders'         => 'orders.view',
        'projects'               => 'projects.view',
        'blog_posts'             => 'posts.view',
        'blog_categories'        => 'posts.view',
        'users'                  => 'users.view',
        'roles'                  => 'roles.view',
        'permissions'            => 'roles.view',
        'model_has_roles'        => 'roles.view',
        'model_has_permissions'  => 'roles.view',
        'role_has_permissions'   => 'roles.view',
        'user_logs'              => 'logs.view',
        'vps_instances'          => 'vps.view',
        'monitored_sites'        => 'sites.view',
        'cloudflare_zones'       => 'cloudflare.view',
        'cloudflare_dns'         => 'cloudflare.view',
        'notes'                  => 'notes.view',
        'calendar_events'        => 'calendar.view',
        'e_moduls'               => 'emodul.view',
        'e_modul_categories'     => 'emodul.view',
        'e_modul_pages'          => 'emodul.view',
        'ai_cs_sessions'         => 'ai_cs.view',
        'ai_cs_messages'         => 'ai_cs.view',
        'ai_cs_settings'         => 'ai_cs.manage',
    ];

    /**
     * Filter table names to only include tables from current database that the user has permission to view.
     */
    public function getCleanTables(?User $user = null): array
    {
        $rawTables = Schema::getTableListing();
        $currentDb = DB::connection()->getDatabaseName();
        $cleanTables = [];

        $isSuperAdmin = $user && $user->hasRole('Super Admin');

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

            // Filter table if user does not have permission
            if ($user && !$isSuperAdmin && isset(self::TABLE_PERMISSION_MAP[$tableName])) {
                $requiredPerm = self::TABLE_PERMISSION_MAP[$tableName];
                if (!$user->can($requiredPerm)) {
                    continue;
                }
            }

            $cleanTables[] = $tableName;
        }

        return array_unique($cleanTables);
    }

    /**
     * Generate a clean Markdown description of all application database tables.
     */
    public function getSchemaSummary(?User $user = null): string
    {
        $tables = $this->getCleanTables($user);
        $schemaText = "# DATABASE SCHEMA & STRUCTURE (AKSES RESMI PENGGUNA)\n\n";

        if (empty($tables)) {
            $schemaText .= "Tidak ada tabel database yang diizinkan untuk diakses oleh akun pengguna ini.\n\n";
            return $schemaText;
        }

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
