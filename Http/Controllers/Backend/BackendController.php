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


        try{
            \Mail::to($to)->send(new FormMail($request, $form));

            return back()->with('success', $form->message_fields->success);
        }catch (\Exception $e){
            $errors[]             = $form->message_fields->failure;
            $errors[]             = $e->getMessage();

            return redirect()->back()->withErrors($errors)->withInput();
        }
    }

    public static function translateDynamicStringOfForms($string, $request)
    {

        $pair = $request;
        $codes = $pair;
        $pattern = '#!%s!#';

        $map = array();
        foreach($codes as $var => $value) {
            $map[sprintf($pattern, $var)] = $value;
        }

        $string = strtr($string, $map);

        return $string;

    }

}
