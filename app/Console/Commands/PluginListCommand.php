<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Core\PluginManager;
use App\Models\Plugin;

class PluginListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugin:list {--sync : Sync plugins before listing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all discovered plugins';

    /**
     * Execute the console command.
     */
    public function handle(PluginManager $manager)
    {
        if ($this->option('sync')) {
            $this->info('Syncing plugins...');
            $manager->syncPlugins();
        }

        $plugins = Plugin::all(['name', 'namespace', 'version', 'status']);

        if ($plugins->isEmpty()) {
            $this->warn('No plugins found.');
            return;
        }

        $this->table(
            ['Name', 'Namespace', 'Version', 'Status'],
            $plugins->toArray()
        );
    }
}
