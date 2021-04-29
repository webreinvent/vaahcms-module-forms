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

function vh_form($slug = null)
{
    $form = \VaahCms\Modules\Forms\Models\ContactForm::where('slug',$slug)
        ->where('is_published',1)
        ->where('vh_theme_id',vh_get_theme_id())
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

    $value = '<form action="'.url('/api/form/submit').'" method="'.$form->method_type.'">'."\n";
    $value .= '<input type="hidden" name="_token" value="'.csrf_token().'" />'."\n";
    $value .= '<input type="hidden" name="id" value="'.$form->id.'" />'."\n";

    foreach ($form->fields as $field){

        $value .= "<div class=\"field\">
                      <label class='label'>".$field->name."</label>
                      <div class=\"control\">";



        switch($field->type->slug){

            case 'tel':
                $value .= "<input class='input' type='number'  name='".\Illuminate\Support\Str::slug($field->name)."'";

                if($field->is_required){
                    $value .= " required ";
                }

                $value .= "placeholder='".$field->name."'>";

                break;

            case 'textarea':
                $value .= "<textarea class=\"textarea\" name='".\Illuminate\Support\Str::slug($field->name)."'";
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

                $value .= 'type="'.$field->type->slug.'" name="'.\Illuminate\Support\Str::slug($field->name).'">
                                <span class="file-cta">
                                  <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                                  </span>
                                  <span class="file-label">
                                    Choose a file…
                                  </span>
                                </span>
                              </label>
                            </div>';
                break;

            case 'drop-down-menu':

                $value .= '<div class="select">
                              <select name="'.Illuminate\Support\Str::slug($field->name).'"';

                if($field->is_required){
                    $value .= " required ";
                }

                $value .= '>
                                <option value="">Select dropdown</option>
                                <option value="ee">With options</option>
                              </select>
                            </div>';

                break;

            case 'checkboxes':
                $value .= '<label class="checkbox">
                              <input type="checkbox" ';

                if($field->is_required){
                    $value .= " required ";
                }

                $value .= '>
                              Remember me
                            </label>';
                break;

            case 'radio-buttons':
                $value .= '<div class="control">
                              <label class="radio">
                                <input type="radio" name="answer">
                                Yes
                              </label>
                              <label class="radio">
                                <input type="radio" name="answer">
                                No
                              </label>
                            </div>';
                break;

            default:
                $value .= "<input class='input' type='".$field->type->slug. "' name='".\Illuminate\Support\Str::slug($field->name)."'";
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

