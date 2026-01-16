<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Core\ThemeManager;
use App\Models\Theme;

class ThemeListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:list {--sync : Sync themes before listing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all discovered themes';

    /**
     * Execute the console command.
     */
    public function handle(ThemeManager $manager)
    {
        if ($this->option('sync')) {
            $this->info('Syncing themes...');
            $manager->syncThemes();
        }

        $themes = Theme::all(['name', 'slug', 'is_active']);

        if ($themes->isEmpty()) {
            $this->warn('No themes found.');
            return;
        }

        $headers = ['Name', 'Slug', 'Active'];
        $data = $themes->map(function($theme) {
            return [
                $theme->name,
                $theme->slug,
                $theme->is_active ? 'YES' : 'no'
            ];
        })->toArray();

        $this->table($headers, $data);
    }
}
