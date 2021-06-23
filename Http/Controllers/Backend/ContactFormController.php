<?php namespace VaahCms\Modules\Forms\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VaahCms\Modules\Cms\Entities\ContentType;
use VaahCms\Modules\Cms\Entities\FieldType;
use VaahCms\Modules\Forms\Models\FormContent;
use VaahCms\Modules\Forms\Models\FormFieldType;
use WebReinvent\VaahCms\Entities\Theme;

class ContactFormController extends Controller
{


    public $theme;

    //----------------------------------------------------------
    public function __construct()
    {
        $this->theme = vh_get_backend_theme();
    }

    public function getAssets(Request $request)
    {

        $data['field_types'] = FormFieldType::select('id', 'name', 'slug', 'meta')
            ->get();

        $data['currency_codes'] = vh_get_currency_list();
        $data['themes'] = Theme::getActiveThemes();

        $data['bulk_actions'] = vh_general_bulk_actions();
        $data['mail'] = config('mail.from');

        $data['form_messages'] = [
            'success' => 'Your message was sent successfully. Thanks.',
            'failure' => 'Failed to send your message.',
            'term' => 'Please accept the terms to proceed.'
        ];

        $response['status'] = 'success';
        $response['data'] = $data;

        return response()->json($response);
    }
    //----------------------------------------------------------
    public function postCreate(Request $request)
    {
        $response = FormContent::postCreate($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getList(Request $request)
    {

        $response = FormContent::getList($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getItem(Request $request, $id)
    {

        $response = FormContent::getItem($id);
        return response()->json($response);

    }
    //----------------------------------------------------------
    public function getItemRelations(Request $request, $id)
    {

        $response = ContentType::getItemWithRelations($id);
        return response()->json($response);

    }
    //----------------------------------------------------------
    public function postStoreGroups(Request $request, $id)
    {
        $response = ContentType::postStoreGroups($request, $id);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function postStore(Request $request,$id)
    {
        $response = FormContent::postStore($request,$id);
        return response()->json($response);
    }

    //----------------------------------------------------------
    //----------------------------------------------------------
    public function postActions(Request $request, $action)
    {
        $rules = array(
            'inputs' => 'required',
        );

        $validator = \Validator::make( $request->all(), $rules);
        if ( $validator->fails() ) {

            $errors             = errorsToArray($validator->errors());
            $response['status'] = 'failed';
            $response['errors'] = $errors;
            return response()->json($response);
        }

        $response = [];

        $response['status'] = 'success';

        $inputs = $request->all();

        switch ($action)
        {

            //------------------------------------
            case 'bulk-change-status':
                $response = FormContent::bulkStatusChange($request);
                break;
            //------------------------------------
            case 'bulk-trash':

                $response = FormContent::bulkTrash($request);

                break;
            //------------------------------------
            case 'bulk-restore':

                $response = FormContent::bulkRestore($request);

                break;

            //------------------------------------
            case 'bulk-delete':

                $response = FormContent::bulkDelete($request);

                break;

            //------------------------------------
        }

        return response()->json($response);

    }


    public function upload(Request $request)
    {

        $allowed_file_upload_size = config('vaahcms.allowed_file_upload_size');

        $input_file_name = null;
        $rules = array(
            'folder_path' => 'required',
            'file' => 'max:'.$allowed_file_upload_size,
        );

        if($request->has('file_input_name'))
        {
            $rules[$request->file_input_name] = 'required';
            $input_file_name = $request->file_input_name;
        } else{
            $rules['file'] = 'required';
            $input_file_name = 'file';
        }





        /*        $validator = \Validator::make( $request->all(), $rules);
                if ( $validator->fails() ) {

                    $errors             = errorsToArray($validator->errors());
                    $response['status'] = 'failed';
                    $response['errors'] = $errors;
                    return response()->json($response);
                }*/

        $params = [
            'folder_path' => 'public/form/media'
        ];

        $request->merge($params);

        try{

            //add year and month folder
            $request->folder_path = $request->folder_path."/".date('Y')."/".date('m');

            foreach ($request->files as $files){
                foreach ($files as $key => $file){
                    $data['uploaded'][$key]['extension'] = $request->file('files')[$key]->extension();
                    $data['uploaded'][$key]['original_name'] = $request->file('files')[$key]->getClientOriginalName();
                    $data['uploaded'][$key]['mime_type'] = $request->file('files')[$key]->getClientMimeType();
                    $type = explode('/',$data['uploaded'][$key]['mime_type']);
                    $data['uploaded'][$key]['type'] = $type[0];
                    $data['uploaded'][$key]['size'] = $request->file('files')[$key]->getSize();

                    if($request->file_name && !is_null($request->file_name)
                        && $request->file_name != 'null')
                    {
                        $upload_file_name = \Str::slug($request->file_name).'.'.$data['uploaded'][$key]['extension'];

                        $upload_file_path = 'storage/app/'.$request->folder_path.'/'.$upload_file_name;

                        $full_upload_file_path = base_path($upload_file_path);

                        //if file already exist then prefix if with microtime
                        if(\File::exists($full_upload_file_path))
                        {
                            $time_stamp = \Carbon\Carbon::now()->timestamp;
                            $upload_file_name = \Str::slug($request->file_name).'-'.$time_stamp.'.'.$data['uploaded'][$key]['extension'];
                        }
                        $path = $request->file('files')[$key]
                            ->storeAs($request->folder_path, $upload_file_name);

                        $data['uploaded'][$key]['name'] = $request->file_name;
                        $data['uploaded'][$key]['uploaded_file_name'] = $data['uploaded'][$key]['name'].'.'.$data['uploaded'][$key]['extension'];

                    } else{


                        $path = $request->file('files')[$key]->store($request->folder_path);

                        $data['uploaded'][$key]['name'] = $data['uploaded'][$key]['original_name'];
                        $data['uploaded'][$key]['uploaded_file_name'] = $data['uploaded'][$key]['name'];
                    }

                    $data['uploaded'][$key]['slug'] = \Str::slug($data['uploaded'][$key]['name']);
                    //$data['extension'] = $name_details['extension'];

                    $data['uploaded'][$key]['path'] = 'storage/app/'.$path;
                    $data['uploaded'][$key]['full_path'] = base_path($data['uploaded'][$key]['path']);

                    $data['uploaded'][$key]['url'] = $path;

                    if (substr($path, 0, 6) =='public') {
                        $data['uploaded'][$key]['url'] = 'storage'.substr($path, 6);
                    }

                    $data['uploaded'][$key]['full_url'] = asset($data['uploaded'][$key]['url']);

                    //create thumbnail if image
                    if($data['uploaded'][$key]['type'] == 'image')
                    {
                        $image = \Image::make($data['uploaded'][$key]['full_path'])->fit(180, 101, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $name_details = pathinfo($data['uploaded'][$key]['full_path']);
                        $thumbnail_name = $name_details['filename'].'-thumbnail.'.$name_details['extension'];
                        $thumbnail_path = $request->folder_path.'/'.$thumbnail_name;
                        \Storage::put($thumbnail_path, (string) $image->encode());

                        if (substr($thumbnail_path, 0, 6) =='public') {
                            $data['uploaded'][$key]['url_thumbnail'] = 'storage'.substr($thumbnail_path, 6);
                        }

                        $data['isImages'][$key] = true;

                    }

                    $data['files'][$key] = basename($path);
                }
            }

            $data['baseurl'] = asset('storage'.substr($request->folder_path, 6)).'/';

            $response['status'] = 'success';
            $response['data'] = $data;

        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();
        }

        return response()->json($response);

    }


}
