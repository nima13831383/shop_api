<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClearExpiredGuestCarts extends Command
{
    protected $signature = 'cart:clear-expired-guest';
    protected $description = 'Clear guest carts older than 7 days';

    public function handle()
    {
        $expiredDate = Carbon::now()->subDays(1); // فقط 5 دقیقه قبل

        $deleted = DB::table('carts')
            ->whereNull('user_id')   // فقط سبد مهمان
            ->where('created_at', '<', $expiredDate)
            ->delete();

        $this->info("Deleted {$deleted} expired guest carts.");
    }
}
