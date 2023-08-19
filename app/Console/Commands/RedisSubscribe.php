<?php

namespace App\Console\Commands;

use App\Jobs\SendEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to a Redis channel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            Redis::subscribe(['send-mail'], function ($message) {
                $emailData = json_decode($message, true);
                SendEmail::dispatch(
                    $emailData['to'],
                    $emailData['data'],
                    $emailData['subject'],
                    $emailData['template']
                );
            });
        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }
}
