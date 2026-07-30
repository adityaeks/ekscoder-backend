<?php

namespace App\Console\Commands;

use App\Models\MonitoredSite;
use App\Services\SiteCheckerService;
use Illuminate\Console\Command;

class CheckMonitoredSitesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform scheduled HTTP and SSL health checks on active monitored sites';

    /**
     * Execute the console command.
     */
    public function handle(SiteCheckerService $checker): int
    {
        $sites = MonitoredSite::where('is_active', true)
            ->get()
            ->filter(function (MonitoredSite $site) {
                if (!$site->last_checked_at) {
                    return true;
                }
                return $site->last_checked_at->diffInMinutes(now()) >= $site->check_interval;
            });

        if ($sites->isEmpty()) {
            $this->info('No sites due for monitoring check.');
            return Command::SUCCESS;
        }

        $this->info("Checking {$sites->count()} site(s)...");

        foreach ($sites as $site) {
            $checker->check($site);
            $this->line(" Checked: {$site->name} ({$site->url}) -> Status: " . strtoupper($site->status) . " ({$site->last_response_time}ms)");
        }

        $this->info('Monitoring check completed.');
        return Command::SUCCESS;
    }
}
