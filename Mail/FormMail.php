<?php


namespace  VaahCms\Modules\Forms\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;


class FormMail extends Mailable
{
    use Queueable, SerializesModels;


    public $request;

    /**
     * Create a new message instance.
     *
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('forms::backend.emails.form');
    }

}
