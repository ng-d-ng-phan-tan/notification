<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $to;
    public $data;
    public $subject;
    public $template;

    public function __construct($to, $data, $subject, $template)
    {
        $this->to = $to;
        $this->data = $data;
        $this->subject = $subject;
        $this->template = $template;
    }

    public function handle()
    {
        $template = 'emails.' . $this->template;
        Mail::send($template, ['data' => $this->data], function ($message) {
            $message->to($this->to)->subject($this->subject);
        });
    }
}
