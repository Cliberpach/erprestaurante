<?php

namespace App\Console\Commands;

use Database\Seeders\landlord\PermissionSeeder;
use Database\Seeders\landlord\RoleSeeder;
use Database\Seeders\tenant\BrandSeeder;
use Database\Seeders\tenant\CategorySeeder;
use Database\Seeders\tenant\ConfigurationSeeder;
use Database\Seeders\tenant\ConsumableBrandSeeder;
use Database\Seeders\tenant\ConsumableCategorySeeder;
use Database\Seeders\tenant\CostCenterSeeder;
use Database\Seeders\tenant\DocumentTypeSeeder;
use Database\Seeders\tenant\PaymentConditionSeeder;
use Database\Seeders\tenant\PaymentMethodSeeder;
use Database\Seeders\tenant\PettyCashSeeder;
use Database\Seeders\tenant\PositionSeeder;
use Database\Seeders\tenant\ProofPaymentSeeder;
use Database\Seeders\tenant\ShiftSeeder;
use Database\Seeders\tenant\SupplierSeeder;
use Database\Seeders\tenant\TenantModuleSeeder;
use Database\Seeders\tenant\TenantUserSeeder;
use Database\Seeders\tenant\TypeFieldSeeder;
use Database\Seeders\tenant\WarehouseSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class RebuildTenantTemplate extends Command
{
    protected $signature   = 'tenant:rebuild-template';
    protected $description = 'Drops all tables in tenancy_template_comandapro_online and re-runs tenant migrations + seeders';

    private string $templateDb = 'tenancy_template_comandapro_online';

    /** Mirrors DatabaseSeeder::runTenantSpecificSeeders() — update both if the list changes. */
    private array $tenantSeeders = [
        ConsumableBrandSeeder::class,
        ConsumableCategorySeeder::class,
        PositionSeeder::class,
        ConfigurationSeeder::class,
        PaymentMethodSeeder::class,
        WarehouseSeeder::class,
        BrandSeeder::class,
        CategorySeeder::class,
        DocumentTypeSeeder::class,
        PettyCashSeeder::class,
        ShiftSeeder::class,
        // Modules must seed before PermissionSeeder so module_children has data
        TenantModuleSeeder::class,
        PermissionSeeder::class,
        RoleSeeder::class,
        SupplierSeeder::class,
        ProofPaymentSeeder::class,
        TypeFieldSeeder::class,
        PaymentConditionSeeder::class,
        CostCenterSeeder::class,
        TenantUserSeeder::class,
    ];

    public function handle(): int
    {
        $user = config('database.connections.landlord.username');
        $pass = config('database.connections.landlord.password');
        $host = config('database.connections.landlord.host', '127.0.0.1');
        $port = config('database.connections.landlord.port', '3306');

        $this->info("Rebuilding template: {$this->templateDb}");

        // 1. Drop tables, views and routines
        $this->line('→ Dropping tables, views and routines...');
        $this->dropAll($user, $pass, $host, $port);
        $this->info('  Done.');

        // 2. Register a temporary connection pointing to the template DB
        Config::set('database.connections.tenant_template', [
            'driver'    => 'mysql',
            'host'      => $host,
            'port'      => $port,
            'database'  => $this->templateDb,
            'username'  => $user,
            'password'  => $pass,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'engine'    => null,
        ]);
        DB::purge('tenant_template');

        // 3. Run tenant migrations against the template
        $this->line('→ Running migrations...');
        Artisan::call('migrate', [
            '--path'     => 'database/migrations/tenant',
            '--database' => 'tenant_template',
            '--force'    => true,
        ], $this->output);

        // 4. Seed:
        //    - Point 'tenant' connection to the template so tenant models write there.
        //    - Set 'tenant' as default so Permission/Role (no explicit connection) also write there.
        //    - Landlord models keep $connection = 'landlord' → untouched.
        $this->line('→ Running seeders...');
        Config::set('database.connections.tenant.database', $this->templateDb);
        DB::purge('tenant');
        DB::setDefaultConnection('tenant');

        foreach ($this->tenantSeeders as $class) {
            $this->line("  Seeding: {$class}");
            $seeder = app()->make($class);
            $seeder->setContainer(app())->setCommand($this);
            app()->call([$seeder, 'run']);
        }

        DB::setDefaultConnection('landlord');

        $this->info('Template rebuilt successfully.');

        return self::SUCCESS;
    }

    private function dropAll(string $user, string $pass, string $host, string $port): void
    {
        // Connect without a database first so we can create it if missing
        $pdo = new \PDO(
            "mysql:host={$host};port={$port};charset=utf8mb4",
            $user,
            $pass,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->templateDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$this->templateDb}`");

        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

        foreach ($pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(\PDO::FETCH_COLUMN) as $table) {
            $pdo->exec("DROP TABLE `{$table}`");
        }

        foreach ($pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'")->fetchAll(\PDO::FETCH_COLUMN) as $view) {
            $pdo->exec("DROP VIEW `{$view}`");
        }

        $routines = $pdo->query(
            "SELECT ROUTINE_NAME, ROUTINE_TYPE
             FROM information_schema.ROUTINES
             WHERE ROUTINE_SCHEMA = '{$this->templateDb}'"
        )->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($routines as $r) {
            $pdo->exec("DROP {$r['ROUTINE_TYPE']} IF EXISTS `{$r['ROUTINE_NAME']}`");
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }
}
