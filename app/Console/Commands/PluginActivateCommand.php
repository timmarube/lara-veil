<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Core\PluginManager;

class PluginActivateCommand extends Command
{
    protected $signature = 'plugin:activate {name}';
    protected $description = 'Activate a plugin';

    public function handle(PluginManager $manager)
    {
        $name = $this->argument('name');
        if ($manager->activate($name)) {
            $this->info("Plugin {$name} activated.");
        } else {
            $this->error("Plugin {$name} not found.");
        }
    }
}
