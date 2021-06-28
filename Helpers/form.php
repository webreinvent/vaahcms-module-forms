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

use Illuminate\Support\Str;

function vh_form($slug = null)
{
    $form = \VaahCms\Modules\Form\Models\FormContent::where('slug',$slug)
        ->where('is_published',1)
        ->with(['fields'])->first();

    if(!$form){
        return false;
    }

    return get_form_field($form);

}
function get_form_field(\VaahCms\Modules\Form\Models\FormContent $form)
{

    if(count($form->fields) === 0){
        return false;
    }

    $url = url('/form/submit');

    if(!$form->is_use_default_url){
        if(strpos($form->action_url, 'https://') !== false
            || strpos($form->action_url, 'http://') !== false){
            $url = $form->action_url;
        }else{
            $url = url('/').$form->action_url;
        }

    }

    $value = Session::get("failed") ? "<article class=\"message is-danger\">
            <div class=\"message-body\">
                <ul><li>".
        Session::get("failed")[0]
                ."</li><li>".
        Session::get("failed")[1]
                ."</li></ul>
            </div>
        </article>":null;

    $value .= Session::get('success') ? "<article class=\"message is-info\">
            <div class=\"message-body\">
                <strong>".Session::get('success')."</strong>
            </div>
        </article>":null;



    $value .= '<form enctype="multipart/form-data" action="'.$url.'" method="'.$form->method_type.'">'."\n";
    $value .= '<input type="hidden" name="_token" value="'.csrf_token().'" />'."\n";
    $value .= '<input type="hidden" name="id" value="'.$form->id.'" />'."\n";

    foreach ($form->fields as $field){

        $value .= "<div class=\"field";

        if($field->meta->is_hidden){
            $value .= " is-hidden";
        }
        $value .= "\"> ";

        $value .= "<label class='label'>".$field->name."</label>";

        $value .= "<div class=\"control\">";



        switch($field->type->slug){

            case 'password':
                $value .= "<input class='input' type='password'
                 name='".\Illuminate\Support\Str::slug($field->name)."'";

                if($field->is_required){
                    $value .= " required ";
                }

                $value .= "placeholder='".$field->name."'>";

                break;

            case 'tel':
                $value .= "<input class='input' type='number' value='".old(\Illuminate\Support\Str::slug($field->name)). "'
                 name='".\Illuminate\Support\Str::slug($field->name)."'";

                if($field->is_required){
                    $value .= " required ";
                }

                $value .= "placeholder='".$field->name."'>";

                break;

            case 'textarea':
                $value .= "<textarea class=\"textarea\" value='".old(\Illuminate\Support\Str::slug($field->name)). "'
                 name='".\Illuminate\Support\Str::slug($field->name)."'";
                if($field->is_required){
                    $value .= " required ";
                }
                $value .= "placeholder='".$field->name."'></textarea>";
                break;

            case 'file':
                $value .= '<div class="file is-boxed">
                              <label class="file-label">
                                <input class="file-input"';

                if($field->is_required){
                    $value .= " required ";
                }

                if($field->meta->is_multiple){
                    $value .= " multiple=\"multiple\" ";
                }

                $value .= 'type="'.$field->type->slug.'" name="'.\Illuminate\Support\Str::slug($field->name).'[]">
                                <span class="file-cta">
                                  <span class="file-icon">
                                    <i class="fa fa-upload"></i>
                                  </span>
                                  <span class="file-label">
                                    Choose a file…
                                  </span>
                                </span>
                              </label>
                            </div>';
                break;

            case 'drop-down-menu':

                $value .= '<div class="select ';

                if($field->meta->is_multiple){
                    $value .= " is-multiple ";
                }

                $value .= '">
                              <select value="'.old(\Illuminate\Support\Str::slug($field->name)). '" name="'.Illuminate\Support\Str::slug($field->name).'"';

                if($field->is_required){
                    $value .= " required ";
                }

                if($field->meta->is_multiple){
                    $value .= " multiple ";
                }

                $value .= '>';

                if(!$field->meta->is_multiple){
                    $value .= '<option value="">Select '.$field->name.'</option>';
                }

                foreach ($field->meta->option as $option){
                    $value .= '<option value="'.$option.'">'.$option.'</option>';
                }

                $value .= '</select>
                            </div>';

                break;

            case 'checkboxes':

                foreach ($field->meta->option as $option){
                    $value .= '<label class="checkbox">
                              <input type="checkbox" value="'.$option. '"
                               name="'.Illuminate\Support\Str::slug($field->name).'[]"';

                    $value .= '>
                              '.$option.'
                              
                            </label> &nbsp;';
                }




                break;

            case 'radio-buttons':
                $value .= '<div class="control">';

                foreach ($field->meta->option as $option){
                    $value .= '<label class="radio">
                                <input type="radio"';

                    if($field->is_required){
                        $value .= " required";
                    }

                    $value .= 'value="'.$option.'" name="'.\Illuminate\Support\Str::slug($field->name).'">
                                '.$option.'
                              </label>';
                }

                $value .= '</div>';
                break;

            default:
                $value .= "<input class='input' type='".$field->type->slug. "' 
                value='".old(\Illuminate\Support\Str::slug($field->name)). "' 
                name='".\Illuminate\Support\Str::slug($field->name)."'";
                if($field->is_required){
                    $value .= " required ";
                }
                $value .= "placeholder='".$field->name."'>";

                break;
        }




        $value .= "</div></div>";

//
    }

    $value .= '<div class="field is-grouped">
                  <div class="control">
                    <button type="submit" class="button is-link">Submit</button>
                  </div>
                </div>';
    $value .= '</form>';

    return $value;

}
//-----------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------

