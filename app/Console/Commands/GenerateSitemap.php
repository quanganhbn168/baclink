<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        SitemapGenerator::create(config('app.url')) // Lấy base URL từ config/app.php
            ->has                 (
                Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_HOURLY),
                Url::create('/about')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY),
                Url::create('/contact')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            )
            ->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
        return Command::SUCCESS;
    }
}