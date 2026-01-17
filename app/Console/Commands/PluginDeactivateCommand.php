<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Core\PluginManager;

class PluginDeactivateCommand extends Command
{
    protected $signature = 'plugin:deactivate {name}';
    protected $description = 'Deactivate a plugin';

    public function handle(PluginManager $manager)
    {
        $name = $this->argument('name');
        if ($manager->deactivate($name)) {
            $this->info("Plugin {$name} deactivated.");
        } else {
            $this->error("Plugin {$name} not found.");
        }
    }
}
