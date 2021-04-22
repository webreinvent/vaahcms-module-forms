<?php namespace VaahCms\Modules\Forms\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VaahCms\Modules\Cms\Entities\ContentType;
use VaahCms\Modules\Cms\Entities\FieldType;
use VaahCms\Modules\Forms\Models\ContactForm;
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

        $response['status'] = 'success';
        $response['data'] = $data;

        return response()->json($response);
    }
    //----------------------------------------------------------
    public function postCreate(Request $request)
    {
        $response = ContentType::postCreate($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getList(Request $request)
    {

        $response = ContactForm::getList($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getItem(Request $request, $id)
    {

        $response = ContactForm::getItem($id);
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
        $response = ContentType::postStore($request,$id);
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
                $response = ContentType::bulkStatusChange($request);
                break;
            //------------------------------------
            case 'bulk-trash':

                $response = ContentType::bulkTrash($request);

                break;
            //------------------------------------
            case 'bulk-restore':

                $response = ContentType::bulkRestore($request);

                break;

            //------------------------------------
            case 'bulk-delete':

                $response = ContentType::bulkDelete($request);

                break;

            //------------------------------------
        }

        return response()->json($response);

    }


}
