<?php

namespace App\Mail\Book;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Book;

class BookSaved extends Mailable
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
    public $subject = 'Data are successfully saved';    

    /**
     *
     * @var Book
     */
    protected $book;
    
    /**
     * Create a new message instance.
     * 
     * @param Book $book
     * @return void
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.book.success')->with('book', $this->book);
    }
    
}
