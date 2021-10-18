<?php  namespace VaahCms\Modules\Forms\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ExtendController extends Controller
{
    public static $link;

    //----------------------------------------------------------
    public function __construct()
    {
        $base_url = route('vh.backend.form')."#/";
        $link = $base_url;
        self::$link = $link;
    }
    //----------------------------------------------------------
    public static function topLeftMenu()
    {
        $links = [];

        $response['status'] = 'success';
        $response['data'] = $links;

        return $response;

    }
    //----------------------------------------------------------
    public static function topRightUserMenu()
    {
        $links = [];

        $response['status'] = 'success';
        $response['data'] = $links;

        return $response;
    }
    //----------------------------------------------------------
    public static function sidebarMenu()
    {
        $links = [];

        $links[0] = [
            'link' => self::$link."content/",
            'icon' => 'file-alt',
            'label'=> 'Form'
        ];

        $response['status'] = 'success';
        $response['data'] = $links;

        return $response;
    }
    //----------------------------------------------------------

}
