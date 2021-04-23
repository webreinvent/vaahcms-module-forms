<?php namespace VaahCms\Modules\Forms\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VaahCms\Themes\Forms\Mail\FormMail;

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
        try{
            \Mail::to(env('MAIL_FROM_ADDRESS'))->send(new FormMail($request));

            return back()->with('success', 'Thanks for contacting us!');
        }catch (\Exception $e){
            $errors[]             = $e->getMessage();

            return back()->with('failed', $errors)->withInput();
        }
    }

}
