<?php

namespace Aleksa\LaravelVisitorsStatistics\Console\Commands;

use Exception;
use Illuminate\Console\Command;

class UpdateMaxMindDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maxmind:update {--scheduled}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update MaxMind GeoLite2 database that is used for determining user location.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('scheduled') && config('visitorstatistics.auto_update') === false) {
            $this->comment('MaxMind database not updated, set auto_update option to true.');

            return false;
        }

        $this->comment('Updating MaxMind database...');

        $databaseDownloadUrl = config('visitorstatistics.database_download_url');
        $saveLocation = config('visitorstatistics.database_location');

        // Check if download URL returns headers 200
        $headers = get_headers($databaseDownloadUrl);

        if (substr($headers[0], 9, 3) != '200') {
            throw new Exception('Unable to download database update, check if the URL is valid.');
        }

        // Download database to temporary directory
        $tmpFile = tempnam(sys_get_temp_dir(), 'maxmind');
        file_put_contents($tmpFile, fopen($databaseDownloadUrl, 'r'));

        // Replace old database with new one
        @unlink($saveLocation);

        file_put_contents($saveLocation, gzopen($tmpFile, 'r'));

        @unlink($tmpFile);

        $this->info('MaxMind database updated.');

        return true;
    }
}
