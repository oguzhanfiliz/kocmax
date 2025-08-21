<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SwaggerDocSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates Swagger API documentation and copies it to the frontend project.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Generating Swagger API documentation...');

        // Step 1: Generate the api-docs.json file
        Artisan::call('l5-swagger:generate');
        $this->info(Artisan::output());

        $this->info('Documentation generated successfully.');

        // Step 2: Copy the generated file to the frontend project
        $sourcePath = storage_path('api-docs/api-docs.json');
        $destinationPath = '/Users/oguzhanfiliz/Desktop/calisma/b2bfront/shofy-vue-nuxt/api-docs.json';

        if (!File::exists($sourcePath)) {
            $this->error('Error: Generated documentation file not found at ' . $sourcePath);
            return 1;
        }

        try {
            // Ensure the destination directory exists
            $destinationDir = dirname($destinationPath);
            if (!File::isDirectory($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
                $this->info('Created destination directory: ' . $destinationDir);
            }

            File::copy($sourcePath, $destinationPath);
            $this->info('Successfully copied api-docs.json to:');
            $this->line($destinationPath);
        } catch (\Exception $e) {
            $this->error('An error occurred while copying the file: ' . $e->getMessage());
            return 1;
        }

        $this->info('Swagger documentation sync process completed successfully!');
        return 0;
    }
}
