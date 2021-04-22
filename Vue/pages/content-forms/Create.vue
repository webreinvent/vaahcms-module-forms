<script src="./CreateJs.js"></script>
<template>
    <div class="column is-8" v-if="page.assets">

        <div class="card">

            <!--header-->
            <header class="card-header">

                <div class="card-header-title">
                    <span>Create New Content Type</span>
                </div>


                <div class="card-header-buttons">

                    <div class="field has-addons is-pulled-right">

                        <p class="control">
                            <b-button icon-left="save"
                                      type="is-light"
                                      :loading="is_btn_loading"
                                      @click="create()">
                                Save
                            </b-button>
                        </p>



                        <p class="control">
                            <b-button tag="router-link"
                                      type="is-light"
                                      :to="{name: 'content.forms.list'}"
                                      icon-left="times">
                            </b-button>
                        </p>



                    </div>


                </div>

            </header>
            <!--/header-->

            <!--content-->
            <div class="card-content">
                <div class="block">

                    <b-field label="Name" :label-position="labelPosition">
                        <b-input v-model="new_item.name"></b-input>
                    </b-field>

                    <b-field label="Slug" :label-position="labelPosition">
                        <b-input v-model="new_item.slug"></b-input>
                    </b-field>

                    <b-field label="Themes"
                             :label-position="labelPosition">

                        <b-select v-model="new_item.vh_theme_id">
                            <option value="">Select a Theme</option>
                            <option v-for="(theme, index) in page.assets.themes"
                                    :value="theme.id"
                            >{{theme.name}}</option>
                        </b-select>


                    </b-field>

                </div>
                <b-field label="Is Published" :label-position="labelPosition">
                    <b-radio-button v-model="new_item.is_published"
                                    :native-value=1>
                        <span>Yes</span>
                    </b-radio-button>

                    <b-radio-button type="is-danger"
                                    v-model="new_item.is_published"
                                    :native-value=0>
                        <span>No</span>
                    </b-radio-button>
                </b-field>

                <b-tabs type="is-boxed">
                    <b-tab-item label="Form">


                        <div class="columns">

                            <div class="column">

                                <div class="card">

                                    <!--header-->
                                    <header class="card-header">

                                        <div class="card-header-title">
                                            <span>Form Structure</span>
                                        </div>

                                    </header>
                                    <!--/header-->

                                    <!--content-->


                                        <div class="card">

                                            <div class="card-content is-paddingless">

                                                <div class="draggable" >
                                                    <draggable class="dropzone" :list="fields"
                                                               :group="{name:'fields'}">
                                                        <div v-if="fields.length>0"
                                                             v-for="(field, f_index) in fields"
                                                             :key="f_index">
                                                            <div class="dropzone-field">
                                                                <b-field class="is-marginless" >
                                                                    <p class="control drag">
                                                                        <span class="button is-static">:::</span>
                                                                    </p>

                                                                    <p class="control " v-if="field.type">
                                                        <span class="button dropzone-field-label is-static">
                                                            {{field.type.name}}
                                                        </span>
                                                                    </p>
                                                                    <b-input v-model="field.name" expanded placeholder="Field Name">
                                                                    </b-input>
                                                                    <b-tooltip label="Copy Slug" type="is-dark">
                                                                        <p class="control">
                                                                            <b-button @click="$vaah.copy(field.slug)"
                                                                            >#{{field.id}}</b-button>
                                                                        </p>
                                                                    </b-tooltip>

                                                                    <b-tooltip label="Field Option" type="is-dark">
                                                                        <p class="control">

                                                                                <b-button icon-left="cog"
                                                                                          @click="toggleFieldOptions($event)"></b-button>

                                                                        </p>
                                                                    </b-tooltip>

                                                                    <b-tooltip label="Delete Field" type="is-dark">
                                                                        <p class="control">

                                                                                <b-button @click="deleteGroupField(fields, f_index)"
                                                                                          icon-left="trash"></b-button>

                                                                        </p>
                                                                    </b-tooltip>

                                                                </b-field>
                                                                <div class="dropzone-field-options ">

                                                                    <table class="table">
                                                                        <tr>
                                                                            <td width="180" >
                                                                                Is required
                                                                            </td>
                                                                            <td>
                                                                                <b-switch v-model="field.is_required" true-value=1
                                                                                          type="is-success">
                                                                                </b-switch>
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td >
                                                                                Excerpt
                                                                            </td>
                                                                            <td>
                                                                                <b-input maxlength="200" v-model="field.excerpt"
                                                                                         type="textarea"></b-input>
                                                                            </td>
                                                                        </tr>

                                                                        <template v-if="field.meta">
                                                                            <tr v-for="(meta_item, meta_index) in field.meta">
                                                                                <td v-html="$vaah.toLabel(meta_index)">
                                                                                </td>
                                                                                <td>
                                                                                    <div v-if="meta_index == 'is_hidden'">
                                                                                        <b-checkbox v-model="field.meta[meta_index]">Is Hidden</b-checkbox>
                                                                                    </div>
                                                                                    <div v-else>
                                                                                        <b-input v-model="field.meta[meta_index]"
                                                                                                 type="text"></b-input>
                                                                                    </div>

                                                                                </td>
                                                                            </tr>
                                                                        </template>

                                                                    </table>



                                                                </div>
                                                            </div>
                                                        </div>

                                                    </draggable>
                                                </div>


                                            </div>


                                        </div>




                                    <!--/content-->




                                </div>

                            </div>
                            <div class="column is-4" >

                                <div class="card">

                                    <!--header-->
                                    <header class="card-header">

                                        <div class="card-header-title">
                                            <span>Form Fields</span>

                                        </div>


                                    </header>
                                    <!--/header-->

                                    <!--content-->
                                    <div class="card-content" style="max-height: 600px; overflow: auto;">



                                        <div class="draggable" >
                                            <draggable :list="page.assets.field_types"
                                                       :clone="cloneField"
                                                       :group="{name:'fields', pull:'clone', put:false}"
                                            >

                                                <div v-for="(field, index) in page.assets.field_types"
                                                     :key="index">
                                                    <b-field class="has-margin-bottom-5" expanded>
                                                        <p class="control drag">
                                                            <span class="button is-static">:::</span>
                                                        </p>

                                                        <p class="control drag">
                                                            <span class="button is-static">{{field.name}}</span>
                                                        </p>
                                                    </b-field>

                                                </div>

                                            </draggable>
                                        </div>

                                    </div>
                                    <!--/content-->





                                </div>

                            </div>


                        </div>



                    </b-tab-item>

                    <b-tab-item label="Mail">
                        <section>
                            <b-field label="Name">
                                <b-input value="Kevin Garvey"></b-input>
                            </b-field>

                            <b-field label="Email"
                                     message="This email is invalid">
                                <b-input type="email"
                                         value="john@"
                                         maxlength="30">
                                </b-input>
                            </b-field>

                            <b-field label="Username"
                                     message="This username is available">
                                <b-input value="johnsilver" maxlength="30"></b-input>
                            </b-field>

                            <b-field label="Password"
                                     :message="['Password is too short', 'Password must have at least 8 characters']">
                                <b-input value="123" type="password" maxlength="30"></b-input>
                            </b-field>

                            <b-field label="Subject">
                                <b-select placeholder="Select a subject">
                                    <option value="1">Option 1</option>
                                    <option value="2">Option 2</option>
                                </b-select>
                            </b-field>
                        </section>
                    </b-tab-item>

                    <b-tab-item label="Messages">
                        What light is light, if Silvia be not seen? <br>
                        What joy is joy, if Silvia be not by— <br>
                        Unless it be to think that she is by <br>
                        And feed upon the shadow of perfection? <br>
                        Except I be by Silvia in the night, <br>
                        There is no music in the nightingale.
                    </b-tab-item>

                    <b-tab-item label="Additional Setting" disabled>
                        Nunc nec velit nec libero vestibulum eleifend.
                        Curabitur pulvinar congue luctus.
                        Nullam hendrerit iaculis augue vitae ornare.
                        Maecenas vehicula pulvinar tellus, id sodales felis lobortis eget.
                    </b-tab-item>
                </b-tabs>
            </div>
            <!--/content-->





        </div>




    </div>
</template>
