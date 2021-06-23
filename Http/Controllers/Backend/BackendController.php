<?php namespace VaahCms\Modules\Forms\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VaahCms\Modules\Forms\Mail\FormMail;
use VaahCms\Modules\Forms\Models\FormContent;

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
        $file = $request->hasFile('tes');

        $attachments = array();

        if($request->file()){


            $forlder_path = 'public/form/media/'.date('Y')."/".date('m');

            foreach ($request->file() as $index => $file){
                $upload_file_name = $file->getClientOriginalName();
                $upload_file_path = 'storage/app/'.$forlder_path.'/'.$upload_file_name;


                $full_folder_path = base_path('storage/app/'.$forlder_path);

                $full_upload_file_path = base_path($upload_file_path);

                $attachments[$index]['file'] = $full_upload_file_path;
                $attachments[$index]['mime_type'] = $file->getMimeType();
                $attachments[$index]['name'] = $upload_file_name;

                if(\File::exists($full_upload_file_path))
                {
                    $time_stamp = \Carbon\Carbon::now()->timestamp;
                    $upload_file_name = \Str::slug($file->getClientOriginalName()).'-'.$time_stamp.'.'.$file->extension();
                }
                $file->move($full_folder_path,$upload_file_name);
            }
        }

        $form = FormContent::where('id',$request->id)->first();

        $to = env('MAIL_FROM_ADDRESS');

        if($form && $form->mail_fields && $form->mail_fields->to){
            $to = self::translateDynamicStringOfForms($form->mail_fields->to,$request->all());
        }

        try{
            \Mail::to($to)->send(new FormMail($request, $form, $attachments));

            return back()->with('success', $form->message_fields->success);
        }catch (\Exception $e){
            $errors[]             = $form->message_fields->failure;
            dd($e);
            $errors[]             = $e->getMessage();

            return back()->with('failed', $errors)->withInput();
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
