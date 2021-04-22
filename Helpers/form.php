<?php

/*
|--------------------------------------------------------------------------
| Naming conventions
|--------------------------------------------------------------------------
|
| <prefix>_<types> (plural): will return list of the items with pagination
| <prefix>_<types>_all (plural): will return list of all items
| <prefix>_<types>_count (plural): will return count of items
| <prefix>_<type>  (singular): will return one single record
| $args = ['select' => '', 'where' => [], ]
|
*/



//-----------------------------------------------------------------------------------

function form_field($slug = null)
{

    $form = \VaahCms\Modules\Forms\Models\ContactForm::where('slug',$slug)
        ->with(['fields'])->first();

    if(!$form){
        return false;
    }

    return get_form_field($form);

}
function get_form_field(\VaahCms\Modules\Forms\Models\ContactForm $form)
{

    if(count($form->fields) === 0){
        return false;
    }

    $value = '<form action="'.url('form/submit').'" method="post">'."\n";
    $value .= '<input type="hidden" name="_token" value="'.csrf_token().'" />'."\n";

    foreach ($form->fields as $field){
        $value .= '<input type="'.$field->slug.'" id="fname" name="'.$field->pivot->name.'">'."\n";
    }

    $value .= '</form>';

    return $value;

}
//-----------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------

