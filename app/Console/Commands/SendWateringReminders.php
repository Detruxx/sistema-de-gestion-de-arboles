<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SendWateringReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-watering-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios trimestrales de riego (hasta 24 meses) para nuevos árboles.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $monthsToCheck = [3, 6, 9, 12, 15, 18, 21, 24];
        $count = 0;

        foreach ($monthsToCheck as $months) {
            $targetDate = Carbon::now()->subMonths($months)->toDateString();
            
            $requests = \App\Models\Request::where('request_type_id', 4)
                ->whereHas('status', function($q) { 
                    $q->where('is_terminal', true); 
                })
                ->whereDate('updated_at', $targetDate)
                ->with('user', 'street')
                ->get();

            foreach ($requests as $req) {
                if ($req->user && !empty($req->user->email)) {
                    Mail::to($req->user->email)->send(new \App\Mail\WateringReminderMail($req, $months));
                    $count++;
                }
            }
        }

        $this->info("Se enviaron $count recordatorios de riego.");
    }
}
