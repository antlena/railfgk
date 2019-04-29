<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExceptionOccured extends Mailable
{
    
    use Queueable, SerializesModels;
    
    /**
     * The "to" recipients of the message.
     *
     * @var array
     */
    public $to = [
        [
            'address' => 'elena.antonova.developer@gmail.com',
            'name' => 'Lena Antonova'
        ]        
    ];
    
    /**
     * The subject of the message.
     *
     * @var string
     */
    public $subject = 'Exception occured!'; 
    
    /**
     * @var string
     */
    protected $exceptionHtml;
    
    /**
     * Create a new message instance.
     * 
     * @param string $exceptionHtml
     * @return void
     */
    public function __construct(string $exceptionHtml)
    {
        $this->exceptionHtml = $exceptionHtml;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('mail.exception')
            ->with('exceptionHtml', $this->exceptionHtml);
    }
    
}
