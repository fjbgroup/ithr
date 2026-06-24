<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IT\ItRequestForm;
use Carbon\Carbon;

class AutoArchiveItRequestForms extends Command
{
    protected $signature = 'it:auto-archive';
    protected $description = 'Auto-archive decided IT request forms older than 30 days';

    public function handle(): int
    {
        $cutoff = Carbon::now()->subDays(30);

        $count = ItRequestForm::whereIn('status', ['Approved', 'Rejected'])
            ->where('is_archived', false)
            ->where('updated_at', '<', $cutoff)
            ->update(['is_archived' => true]);

        $this->info("Auto-archived {$count} IT request form(s) older than 30 days.");

        return self::SUCCESS;
    }
}
