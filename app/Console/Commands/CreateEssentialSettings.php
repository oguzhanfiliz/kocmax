<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class CreateEssentialSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:create-essential 
                            {--force : Force create even if settings exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create essential settings for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Creating essential settings...');
        
        try {
            if ($this->option('force')) {
                $this->warn('⚠️  Force mode enabled - existing settings will be overwritten');
                
                // Delete existing essential settings if force
                Setting::whereIn('key', Setting::ESSENTIAL_SETTINGS)->delete();
            }
            
            Setting::createEssentialSettings();
            
            $this->info('✅ Essential settings created successfully!');
            
            $this->table(
                ['Setting Key', 'Label', 'Group', 'Type'],
                Setting::whereIn('key', Setting::ESSENTIAL_SETTINGS)
                    ->get(['key', 'label', 'group', 'type'])
                    ->map(fn($setting) => [
                        $setting->key,
                        $setting->label,
                        $setting->group,
                        $setting->type
                    ])
                    ->toArray()
            );
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to create essential settings: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
