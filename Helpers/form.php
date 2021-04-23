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

    $value = '<form action="'.url('/api/form/submit').'" method="post">'."\n";
    $value .= '<input type="hidden" name="_token" value="'.csrf_token().'" />'."\n";
    $value .= '<input type="hidden" name="id" value="'.$form->id.'" />'."\n";

    foreach ($form->fields as $field){

        $value .= "<div class=\"field\">
                      <label class='label'>".$field->name."</label>
                      <div class=\"control\">";



        switch($field->type->slug){

            case 'tel':
                $value .= "<input class='input' type='number'  name='".$field->name."'";

                if($field->is_required){
                    $value .= " required ";
                }

                $value .= "placeholder='".$field->name."'>";

                break;

            case 'textarea':
                $value .= "<textarea class=\"textarea\" name='".$field->name."' placeholder='".$field->name."'></textarea>";
                break;

            case 'file':
                $value .= '<div class="file is-boxed">
                              <label class="file-label">
                                <input class="file-input" type="'.$field->type->slug.'" name="'.$field->name.'">
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
                              <select>
                                <option>Select dropdown</option>
                                <option>With options</option>
                              </select>
                            </div>';

                break;

            case 'checkboxes':
                $value .= '<label class="checkbox">
                              <input type="checkbox">
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
                $value .= "<input class='input' type='".$field->type->slug. "' name='".$field->name."'";
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

