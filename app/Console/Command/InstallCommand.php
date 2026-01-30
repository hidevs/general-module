<?php

namespace Modules\General\Console\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;

class InstallCommand extends Command
{
    protected $signature = 'app:install {--fresh : Run migrations fresh} {--module-seed : Use module:seed instead of db:seed}';

    protected $description = 'Initialize and install the application after deployment';

    protected int $step = 1;

    public function handle(): void
    {
        $this->displayBanner();

        $this->generateAppKey();
        $this->createStorageLink();
        $this->clearCaches();

        $this->generateNotifierDocs();

        $this->installRequirements();

        if ($this->option('fresh')) {
            $this->runFreshMigrations();
            $this->seedDatabase();
        } else {
            $this->runMigrations();
        }

        $this->removeLogFiles();

        $this->optimizeApplication();

        $this->configuringApplication();

        $this->info('Installation completed successfully!');
    }

    protected function displayBanner(): void
    {
        $this->info("\033[38;5;197m"); // Hot Pink (matching HiDevs logo)
        $this->info("\033[38;5;197m██╗  ██╗██╗ ██████╗ ███████╗██╗   ██╗███████╗");
        $this->info("\033[38;5;197m██║  ██║██║ ██╔══██╗██╔════╝██║   ██║██╔════╝");
        $this->info("\033[38;5;197m███████║██║ ██║  ██║█████╗  ██║   ██║███████╗");
        $this->info("\033[38;5;197m██╔══██║██║ ██║  ██║██╔══╝  ╚██╗ ██╔╝╚════██║");
        $this->info("\033[38;5;197m██║  ██║██║ ██████╔╝███████╗ ╚████╔╝ ███████║");
        $this->info("\033[38;5;197m╚═╝  ╚═╝╚═╝ ╚═════╝ ╚══════╝  ╚═══╝  ╚══════╝");
        $this->info("\033[38;5;197m╔════════════════════════════════════════════╗\033[0m");
        $this->info("\033[38;5;197m║   \033[1;97mWelcome to HiDevs Installation Wizard    \033[38;5;197m║\033[0m");
        $this->info("\033[38;5;197m╚════════════════════════════════════════════╝\033[0m");
        $this->info('');
    }

    protected function printStep($message): void
    {
        $this->alert("Step {$this->step}: $message");
        $this->step++; // Increment the step after printing
    }

    protected function generateAppKey(): void
    {
        $this->printStep('Generating application key...');
        if (empty(config('app.key'))) {
            $this->call('key:generate', ['--force' => true]);
        }
    }

    protected function createStorageLink(): void
    {
        $this->printStep('Creating symbolic link for storage...');
        $this->call('storage:unlink');
        $this->call('storage:link');
    }

    protected function clearCaches(): void
    {
        $this->printStep('Clearing all caches...');
        if (config('cache.default') == 'redis') {
            Redis::command('flushall');
        }
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->call('optimize:clear');
    }

    public function generateNotifierDocs(): void
    {
        if (array_key_exists('sajya:docs', Artisan::all())) {
            $this->printStep('Generate Notifier documents...');
            $this->call('sajya:docs', [
                'route' => 'general.notifier.rpc',
                '--path' => '/api/notifier/rpc/docs/',
                '--name' => 'index.html',
            ]);
        }
    }

    public function installRequirements(): void
    {
        $this->printStep('Install modules requirements.sh files...');
        $requirements = glob(base_path('Modules/*/requirements.sh'));

        foreach ($requirements as $sellPath) {
            exec("sh {$sellPath}");
        }
    }

    protected function runMigrations(): void
    {
        $this->printStep('Running database migrations...');
        $this->call('migrate', ['--force' => true]);
    }

    protected function runFreshMigrations(): void
    {
        $this->printStep('Running fresh migrations...');
        $this->call('migrate:refresh', ['--force' => true]);
    }

    protected function seedDatabase(): void
    {
        $this->printStep('Seeding the database...');
        $this->call('db:seed', ['--force' => true]);
    }

    protected function optimizeApplication(?bool $clear = null): void
    {
        $this->printStep('Optimizing the application...');
        if (is_null($clear)) {
            $this->call('optimize:clear');
            $this->call('optimize');
        } else {
            $this->call($clear ? 'optimize:clear' : 'optimize');
        }
    }

    protected function configuringApplication(): void
    {
        $this->printStep('Configuring application...');

        //
    }

    public function removeLogFiles(): void
    {
        $this->printStep('Removing log files...');
        $logFilesPath = storage_path('logs/*.log');
        exec("rm -rf {$logFilesPath}");
    }
}
