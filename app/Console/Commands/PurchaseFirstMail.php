<?php

namespace App\Console\Commands;

use App\Mail\PromotionMail;
use App\Models\PurchaseMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PurchaseFirstMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:purchase-first-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send first purchase promotion email to the premium members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mails = PurchaseMail::with('user')->where('mail','<', 7)->get();
        foreach($mails as $mail){
            Mail::to($mail->user->email)->send(new PromotionMail($mail->user->name,($mail->mail + 1)));
            $mail->increment('mail');
            Log::info($mail->user);
        }
    }
}
