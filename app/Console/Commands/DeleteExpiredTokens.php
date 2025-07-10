<?php

namespace App\Console\Commands;

use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DeleteExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa các token đã hết hạn';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = PersonalAccessToken::where('expires_at', '<', now())->delete();
        $this->info("Đã xóa $count token hết hạn.");
    }
}
