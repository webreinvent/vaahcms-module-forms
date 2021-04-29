<?php namespace VaahCms\Modules\Forms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use WebReinvent\VaahCms\Entities\Theme;
use WebReinvent\VaahCms\Entities\ThemeTemplate;
use WebReinvent\VaahCms\Entities\User;
use WebReinvent\VaahCms\Traits\CrudWithUuidObservantTrait;

class ContactForm extends Model {

    use SoftDeletes;
    use CrudWithUuidObservantTrait;

    //-------------------------------------------------
    protected $table = 'vh_form_contact_forms';
    //-------------------------------------------------
    protected $dates = [
        'is_published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    //-------------------------------------------------
    protected $dateFormat = 'Y-m-d H:i:s';
    //-------------------------------------------------
    protected $fillable = [
        'uuid',
        'vh_theme_id',
        'name',
        'slug',
        'is_published',
        'is_use_default_url',
        'action_url',
        'method_type',
        'meta',
        'mail_fields',
        'message_fields',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    //-------------------------------------------------
    //-------------------------------------------------

    //-------------------------------------------------
    public function setSlugAttribute($value)
    {
        if($value)
        {
            $this->attributes['slug'] = Str::slug($value);
        } else{
            $this->attributes['slug'] = null;
        }
    }
    //-------------------------------------------------
    public function setMetaAttribute($value)
    {
        if($value)
        {
            $this->attributes['meta'] = json_encode($value);
        } else{
            $this->attributes['meta'] = null;
        }
    }
    //-------------------------------------------------
    public function getMetaAttribute($value)
    {
        if($value)
        {
            return json_decode($value);
        }
        return null;
    }
    //-------------------------------------------------
    public function getIsUseDefaultUrlAttribute($value)
    {
        if($value == 1)
        {
            return true;
        }
        return false;
    }
    //-------------------------------------------------
    public function setMailFieldsAttribute($value)
    {
        if($value)
        {
            $this->attributes['mail_fields'] = json_encode($value);
        } else{
            $this->attributes['mail_fields'] = null;
        }
    }
    //-------------------------------------------------
    public function getMailFieldsAttribute($value)
    {
        if($value)
        {
            return json_decode($value);
        }
        return null;
    }
    //-------------------------------------------------
    public function setMessageFieldsAttribute($value)
    {
        if($value)
        {
            $this->attributes['message_fields'] = json_encode($value);
        } else{
            $this->attributes['message_fields'] = null;
        }
    }
    //-------------------------------------------------
    public function getMessageFieldsAttribute($value)
    {
        if($value)
        {
            return json_decode($value);
        }
        return null;
    }
    //-------------------------------------------------
    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }
    //-------------------------------------------------
    public function scopeExclude($query, $columns)
    {
        return $query->select( array_diff( $this->getTableColumns(),$columns) );
    }
    //-------------------------------------------------
    public function scopeIsPublished($query)
    {
        return $query->where( 'is_published', 1 );
    }
    //-------------------------------------------------
    public function createdByUser()
    {
        return $this->belongsTo(User::class,
            'created_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function updatedByUser()
    {
        return $this->belongsTo(User::class,
            'updated_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }
    //-------------------------------------------------
    public function deletedByUser()
    {
        return $this->belongsTo(User::class,
            'deleted_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }
    //-------------------------------------------------
    public function theme()
    {
        return $this->belongsTo(Theme::class,
            'vh_theme_id', 'id'
        );
    }
    //-------------------------------------------------
    public function fields()
    {
        return $this->hasMany(ContactFormField::class,
            'vh_form_contact_form_id', 'id'
        )->orderBy('sort', 'asc');
    }
    //-------------------------------------------------
    public static function postCreate($request)
    {

        $validation = static::validation($request);
        if(isset($validation['status']) && $validation['status'] == 'failed')
        {
            return $validation;
        }

        $inputs = $request->all();

        $item = new static();

        $fillable['name'] = $inputs['name'];
        $fillable['slug'] = $inputs['slug'];
        $fillable['vh_theme_id'] = $inputs['vh_theme_id'];
        $fillable['is_published'] = $inputs['is_published'];
        $fillable['mail_fields'] = $inputs['mail_fields'];
        $fillable['message_fields'] = $inputs['message_fields'];

        $item->fill($fillable);
        $item->save();

        ContactFormField::syncWithFormFields($item, $inputs['fields']);

        $response['status'] = 'success';
        $response['data']['item'] =$item;
        $response['messages'][] = 'Saved';

        return $response;

    }
    //-------------------------------------------------
    public static function getList($request)
    {

        if($request['sort_by'])
        {
            $list = static::orderBy($request['sort_by'], $request['sort_order']);
        }else{
            $list = static::orderBy('id', $request['sort_order']);
        }

        if($request['trashed'] == 'true')
        {

            $list->withTrashed();
        }

        if(isset($request->from) && isset($request->to))
        {
            $list->whereBetween('updated_at',[$request->from." 00:00:00",$request->to." 23:59:59"]);
        }

        if($request['filter'])
        {
            $list->where('status',$request['filter']);
        }

        if(isset($request->q))
        {
            $search_array = explode(" ",$request->q);

            foreach ($search_array as $item){
                $list->where(function ($q) use ($item){
                    $q->where('name', 'LIKE', '%'.$item.'%')
                        ->orWhere('id', 'LIKE', $item.'%')
                        ->orWhere('slug', 'LIKE', '%'.$item.'%');
                });
            }
        }


        $data['list'] = $list->with('theme')->paginate(config('vaahcms.per_page'));

/*
        $status = ContentType::where('id', $request->content_type->id);

        $status_list = $status->select('content_statuses')->first();*/



        $response['status'] = 'success';
        $response['data'] = $data;
//        $response['status'] = $status_list;

        return $response;


    }
    //-------------------------------------------------
    public static function validation($request)
    {
        $rules = array(
            'name' => 'required|max:255',
            'slug' => 'required',
            'is_published' => 'required',
            'vh_theme_id' => 'required',
            'method_type' => 'required',
            'fields' => 'required|array',
            'fields.*.name' => 'required|max:100',
        );

        $validator = \Validator::make( $request->all(), $rules);
        if ( $validator->fails() ) {

            $errors             = errorsToArray($validator->errors());
            $response['status'] = 'failed';
            $response['errors'] = $errors;
            return $response;
        }

        $data = [];

        $response['status'] = 'success';

        return $response;

    }
    //-------------------------------------------------
    public static function getItem($id)
    {

        $item = static::where('id', $id)
            ->with(['theme', 'createdByUser', 'updatedByUser', 'deletedByUser'])
            ->with(['fields' => function($f){
                $f->orderBy('sort', 'asc')->with(['type']);
            }])
            ->withTrashed()
            ->first();

        $response['status'] = 'success';
        $response['data'] = $item;

        return $response;

    }
    //-------------------------------------------------
    public static function getFormGroups(Content $content, $type, array $fields=null)
    {
        $groups = [];

        if($type=='content')
        {
            $groups = $content->contentType->groups;
        } else{
            $groups = $content->template->groups;
        }


        $i = 0;
        foreach ($groups as $group)
        {

            $groups[$i] = $group;

            $y = 0;
            foreach ($group->fields as $field)
            {
                $groups[$i]['fields'][$y] = $field;
                $groups[$i]['fields'][$y]['type'] = $field->type;


                $groups[$i]['fields'][$y]['vh_cms_form_field_id'] = null;
                $groups[$i]['fields'][$y]['content'] = null;
                $groups[$i]['fields'][$y]['content_meta'] = null;



                $field_content = ContentFormField::where('vh_cms_content_id', $content->id);
                $field_content->where('vh_cms_form_group_id', $group->id);
                $field_content->where('vh_cms_form_field_id', $field->id);
                $field_content = $field_content->first();


                if($field_content)
                {
                    $groups[$i]['fields'][$y]['vh_cms_form_field_id'] = $field_content->id;
                    $groups[$i]['fields'][$y]['content'] = $field_content->content;
                    $groups[$i]['fields'][$y]['content_meta'] = $field_content->meta;
                }


                $y++;
            }

            $i++;
        }

        return $groups;
    }
    //-------------------------------------------------
    //-------------------------------------------------
    public static function postStore($request,$id)
    {

        $validation = static::validation($request);
        if(isset($validation['status']) && $validation['status'] == 'failed')
        {
            return $validation;
        }

        $name_exist = static::where('id','!=',$request['id'])->where('name',$request['name'])->first();

        if($name_exist)
        {
            $response['status'] = 'failed';
            $response['errors'][] = "This name is already exist.";
            return $response;
        }

        $inputs = $request->all();

        $item = static::where('id',$id)->withTrashed()->first();

        $item->fill($inputs);
        $item->slug = Str::slug($inputs['name']);
        $item->save();


        ContactFormField::syncWithFormFields($item, $inputs['fields']);


        $response['status'] = 'success';
        $response['data'] = [];
        $response['messages'][] = 'Data updated.';

        return $response;

    }
    //-------------------------------------------------
    public static function storeFormGroups(Content $content, $groups)
    {
        $i = 0;
        foreach ($groups as $group)
        {

            $groups[$i] = $group;

            $y = 0;
            foreach ($group['fields'] as $field)
            {
                $stored_field = null;
                if(isset($field['vh_cms_form_field_id']) && !empty($field['vh_cms_form_field_id']))
                {
                    $stored_field = ContentFormField::find($field['vh_cms_form_field_id']);
                }

                if(!$stored_field)
                {
                    $stored_field = new ContentFormField();
                    $stored_field->vh_cms_content_id = $content->id;
                    $stored_field->vh_cms_form_group_id = $group['id'];
                    $stored_field->vh_cms_form_field_id = $field['id'];
                }

                if(is_array($field['content']) || is_object($field['content']))
                {
                    $field['content'] = json_encode($field['content']);
                }

                if($field['type']['slug'] == 'user' && $field['content']){

                    $user = $user_id = User::where('email',$field['content'])->first();

                    if($user)
                    {
                        $stored_field->content = $user->id;
                    }

                }else{
                    $stored_field->content = $field['content'];
                }

                $stored_field->meta = $field['meta'];
                try{
                    $stored_field->save();
                }catch(\Exception $e)
                {
                    $response['status'] = 'failed';
                    $response['inputs'] = $field;
                    $response['errors'][] = $e->getMessage();
                    return $response;
                }


                $y++;
            }

            $i++;
        }

    }
    //-------------------------------------------------
    public static function bulkStatusChange($request)
    {
        if(!$request->has('inputs'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select IDs';
            return $response;
        }

        if(!$request->has('data'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select Status';
            return $response;
        }

        foreach($request->inputs as $id)
        {
            $role = static::where('id',$id)->withTrashed()->first();

            if($role->deleted_at){
                continue ;
            }

            if($request['data']){
                $role->is_published = $request['data'];
            }else{
                if($role->is_published == 1){
                    $role->is_published = 0;
                }else{
                    $role->is_published = 1;
                }
            }
            $role->save();
        }

        $response['status'] = 'success';
        $response['data'] = [];
        $response['messages'][] = 'Action was successful';

        return $response;


    }
    //-------------------------------------------------
    public static function bulkTrash($request)
    {

        if(!$request->has('inputs'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select IDs';
            return $response;
        }


        foreach($request->inputs as $id)
        {
            $item = static::withTrashed()->where('id', $id)->first();
            if($item)
            {
                $item->delete();
            }
        }

        $response['status'] = 'success';
        $response['data'] = [];
        $response['messages'][] = 'Action was successful';

        return $response;


    }
    //-------------------------------------------------
    public static function bulkRestore($request)
    {


        if(!$request->has('inputs'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select IDs';
            return $response;
        }

        foreach($request->inputs as $id)
        {
            $item = static::withTrashed()->where('id', $id)->first();
            if(isset($item) && isset($item->deleted_at))
            {
                $item->restore();
            }
        }

        $response['status'] = 'success';
        $response['data'] = [];
        $response['messages'][] = 'Action was successful';

        return $response;

    }
    //-------------------------------------------------
    public static function bulkDelete($request)
    {

        if(!$request->has('inputs'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select IDs';
            return $response;
        }

        if(!$request->has('data'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select Status';
            return $response;
        }

        foreach($request->inputs as $id)
        {
            $item = static::where('id', $id)->withTrashed()->first();
            if($item)
            {

                $item->forceDelete();

            }
        }

        $response['status'] = 'success';
        $response['data'] = [];
        $response['messages'][] = 'Action was successful';

        return $response;
    }
    //-------------------------------------------------
    public static function getContents($content_type_slug, $args)
    {

        $content_type = ContentType::where('slug', $content_type_slug)->first();



        $contents = static::where('vh_cms_content_type_id', $content_type->id);

        /*if($args['content_groups']) {

            $contents->whereHas('fields.group', function ($f) use ($args) {

                $group_slugs = array_keys($args['content_groups']);
                $f->whereIn('slug', $group_slugs);
            });

        }

        $contents->with(['fields.group.groupable']);*/

        /*if($args['content_groups'])
        {
            foreach($args['content_groups'] as $group)
            {
                $contents->whereHas('fields',  function ($g) use ($group){
                    $g->where('slug', $group['slug']);
                });
            }
            $contents->with('groups');
        }*/


        $contents = $contents->paginate(1);


        return $contents;

    }
    //-------------------------------------------------
    public static function getContent($id, $args, $output)
    {
        $content = static::find($id);

        $response = null;

        $content_groups = static::getFormGroups($content, 'content');

        switch ($output){

            case 'html':
                $response = static::getFormGroupsHtml($content_groups, 'get-the-content');
                break;

            default:
                $response = $content_groups;
                break;
        }


        return $response;
    }
    //-------------------------------------------------
    public static function getTheContent($id, $args)
    {
        return static::getContent($id, $args, 'html');
    }
    //-------------------------------------------------
    public static function getFormGroupsHtml($groups, $custom_class=null, $view=null)
    {
        $html = "";

        if(!$custom_class)
        {
            $custom_class = 'get-the-content';
        }

        if(!$view)
        {
            $view = 'cms::frontend.templates.contents.get-the-content';
        }

        $html = \View::make($view)->with('groups', $groups)
            ->with('custom_class', $custom_class)
            ->render();

        return $html;
    }
    //-------------------------------------------------
    public static function getContentField($item, $group_slug, $field_slug)
    {

        $group = FormGroup::where('slug', $group_slug)->whereHasMorph('groupable',
            [ContentType::class], function ($c) use ($item){
                //$c->where('id', $item->vh_cms_content_id);
        })->first();

        echo "<pre>";
        print_r($group->toArray());
        echo "</pre>";

    }
    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------

}
