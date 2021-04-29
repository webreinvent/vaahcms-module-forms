<?php namespace VaahCms\Modules\Forms\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VaahCms\Modules\Forms\Mail\FormMail;
use VaahCms\Modules\Forms\Models\ContactForm;

class BackendController extends Controller
{


    public function __construct()
    {

    }

    public function index()
    {
        return view('forms::backend.pages.app');
    }

    public function getAssets(Request $request)
    {
        $data=[];

        $response['status'] = 'success';
        $response['data'] = $data;

        return response()->json($response);

    }

    public function formSubmit(Request $request)
    {

        $form = ContactForm::where('id',$request->id)->first();

        $to = env('MAIL_FROM_ADDRESS');

        if($form && $form->mail_fields && $form->mail_fields->to){
            $to = $form->mail_fields->to;
        }


        try{
            \Mail::to($to)->send(new FormMail($request));

            return back()->with('success', 'Thanks for contacting us!');
        }catch (\Exception $e){
            $errors[]             = $e->getMessage();

            dd($e->getMessage());

            return back()->with('failed', $errors)->withInput();
        }
    }

}
