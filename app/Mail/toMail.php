<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class toMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
	public $btn;
	public $name;
	public $subject;
	public $body;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url="",$btn="",$name="",$subject="",$body="")
    {
        $this->url = $url;
		$this->btn = $btn;
		$this->name = $name;
		$this->subject = $subject;
		$this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_NAME'))
			   ->view('mails.mailService')->with(array(
				   'url' => $this->url,
				   'btn' => $this->btn,
				   'name' => $this->name,
				   'subject' => $this->subject,
				   'body' => $this->body
		));
    }
}
