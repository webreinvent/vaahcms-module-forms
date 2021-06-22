<?php


namespace  VaahCms\Modules\Forms\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use VaahCms\Modules\Forms\Http\Controllers\Backend\BackendController;
use VaahCms\Modules\Forms\Models\ContactForm;


class FormMail extends Mailable
{
    use Queueable, SerializesModels;


    public $request;
    public $form;
    public $files;

    /**
     * Create a new message instance.
     *
     * @param $request
     */
    public function __construct($request, ContactForm $form, $attachments)
    {
        $this->request = $request;
        $this->form = $form;
        $this->files = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->form && $this->form->mail_fields){
            $from_name = env('APP_NAME');

            if($this->form->mail_fields->from->name){
                $from_name = BackendController::translateDynamicStringOfForms($this->form->mail_fields->from->name, $this->request->all());
            }

            if($this->form->mail_fields->from->email){

                $from_email = BackendController::translateDynamicStringOfForms($this->form->mail_fields->from->email, $this->request->all());

                $this->from($from_email,$from_name);
            }

            if($this->form->mail_fields->subject){
                $subject = BackendController::translateDynamicStringOfForms($this->form->mail_fields->subject, $this->request->all());
                $this->subject($subject);
            }
        }

        foreach ($this->files as $attachment){
            $this->attach($attachment['file'],[
                'as' => $attachment['name'], // If you want you can chnage original name to custom name
                'mime' => $attachment['mime_type']
            ]);
        }



        return $this->view('forms::backend.emails.form');
    }

}
