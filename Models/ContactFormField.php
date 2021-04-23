<?php namespace VaahCms\Modules\Forms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use VaahCms\Modules\Cms\Entities\FieldType;
use VaahCms\Modules\Cms\Entities\FormField;
use VaahCms\Modules\Cms\Entities\FormGroup;
use WebReinvent\VaahCms\Entities\User;
use WebReinvent\VaahCms\Traits\CrudWithUuidObservantTrait;

class ContactFormField extends Model {

    use SoftDeletes;
    use CrudWithUuidObservantTrait;

    //-------------------------------------------------
    protected $table = 'vh_form_contact_form_fields';
    //-------------------------------------------------
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    //-------------------------------------------------
    protected $dateFormat = 'Y-m-d H:i:s';
    //-------------------------------------------------
    protected $fillable = [
        'uuid',
        'vh_form_field_type_id',
        'vh_form_contact_form_id',
        'sort',
        'name',
        'slug',
        'meta',
        'is_required',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    //-------------------------------------------------
    protected $appends  = [
    ];
    //-------------------------------------------------
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
    //-------------------------------------------------
    public function type()
    {
        return $this->belongsTo(FormFieldType::class,
            'vh_form_field_type_id', 'id'
        );
    }
    //-------------------------------------------------
    public static function deleteItem($id)
    {

        //delete content fields
        self::where('vh_form_contact_form_id', $id)->forceDelete();

        //delete group
        static::where('id', $id)->forceDelete();

    }
    //-------------------------------------------------
    public static function deleteItems($ids_array){

        foreach ($ids_array as $id)
        {
            static::deleteItem($id);
        }

    }

    public static function syncWithFormFields(ContactForm $form, $fields_array){


        //delete form group fields which are just removed
        $stored_group_fields = static::where('vh_form_contact_form_id', $form->id)
            ->get()
            ->pluck('id')
            ->toArray();

        $input_group_fields = collect($fields_array)->pluck('id')->toArray();
        $fields_to_delete = array_diff($stored_group_fields, $input_group_fields);

        if(count($fields_to_delete) > 0)
        {
            static::deleteItems($fields_to_delete);
        }


        if(count($fields_array) > 0 )
        {
            foreach ($fields_array as $f_index => $field)
            {
                if(isset($field['id']))
                {
                    $stored_field = static::find($field['id']);

                } else{
                    $stored_field = new static();
                }

                if(isset($field['type']) && isset($field['type']['slug']) )
                {
                    $type = FormFieldType::where('slug', $field['type']['slug'])->first();
                    if($type)
                    {
                        $field['vh_form_field_type_id'] = $type->id;
                    }

                    unset($field['type']);
                }


                $stored_field->fill($field);
                $stored_field->sort = $f_index;
                $stored_field->slug = $field['slug'];
                $stored_field->vh_form_contact_form_id = $form->id;
                $stored_field->save();
            }
        }


    }
    //-------------------------------------------------

    //-------------------------------------------------

}
