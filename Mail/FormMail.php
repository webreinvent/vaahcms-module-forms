<?php


namespace  VaahCms\Modules\Forms\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use VaahCms\Modules\Forms\Models\ContactForm;


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

        $form = ContactForm::where('id',$this->request->id)->first();

        if($form && $form->mail_fields){
            $from_name = env('APP_NAME');

            if($form->mail_fields->from->name){
                $from_name = $form->mail_fields->from->name;
            }

            if($form->mail_fields->from->email){
                $this->from($form->mail_fields->from->email,$from_name);
            }

            if($form->mail_fields->subject){
                $this->subject($form->mail_fields->subject);
            }
        }

        return $this->view('forms::backend.emails.form');
    }

}
