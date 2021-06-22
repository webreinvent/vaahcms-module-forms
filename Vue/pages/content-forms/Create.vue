<script src="./CreateJs.js"></script>
<template>
    <div class="column is-8" v-if="page.assets">

        <div class="card">

            <!--header-->
            <header class="card-header">

                <div class="card-header-title">
                    <span>Create New Form</span>
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

                    <b-field label="Name" :label-position="label_position">
                        <b-input v-model="new_item.name"></b-input>
                    </b-field>

                    <b-field label="Slug" :label-position="label_position">
                        <b-input v-model="new_item.slug"></b-input>
                    </b-field>

                </div>
                <b-field label="Is Published" :label-position="label_position">
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

                <b-field  expanded
                          grouped
                          label="Action Url"
                          :label-position="label_position">

                    <b-field expanded>
                        <b-radio-button v-model="new_item.is_use_default_url"
                                        type="is-success"
                                        :native-value=true >
                            <span>Use Default Url</span>
                        </b-radio-button>
                        <b-radio-button
                                v-model="new_item.is_use_default_url"
                                :native-value=false>
                            <span>Custom</span>
                        </b-radio-button>

                        <b-input v-if="!new_item.is_use_default_url"
                                 v-model="new_item.action_url"
                                 expanded type="text"
                                 placeholder="Type Action Text">
                        </b-input>
                    </b-field>


                </b-field>

                <b-field label="Method Type"
                         :label-position="label_position">

                    <b-select v-model="new_item.method_type">
                        <option value=null>Select a Method Type</option>
                        <option value="POST">POST</option>
                        <option value="GET">GET</option>
                        <option value="DELETE">DELETE</option>
                        <option value="PUT">PUT</option>

                    </b-select>


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

                                                <div class="draggable" style="background-color: #fafafa">

                                                    <draggable class="dropzone" :list="new_item.fields"
                                                               :group="{name:'fields'}">
                                                        <div v-if="new_item.fields.length>0"
                                                             v-for="(field, f_index) in new_item.fields"
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

                                                                                <b-button @click="deleteGroupField(new_item, f_index)"
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
                                                                                    <div v-if="meta_index.includes('is_')">
                                                                                        <b-checkbox v-model="field.meta[meta_index]">{{$vaah.toLabel(meta_index)}}</b-checkbox>
                                                                                    </div>
                                                                                    <div v-else-if="meta_index === 'option'">
                                                                                        <b-taginput
                                                                                                v-model="field.meta[meta_index]"
                                                                                                ellipsis
                                                                                                placeholder="Add a tag"
                                                                                                aria-close-label="Delete this tag">
                                                                                        </b-taginput>
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
                                                    <p v-if="new_item.fields.length === 0"
                                                       class="has-text-centered has-text-weight-bold">
                                                        DROP HERE
                                                    </p>
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
                            <div class="mt-5">
                                <div class="block">

                                    <b-field label="To" :label-position="label_position">
                                        <b-input v-model="new_item.mail_fields.to" placeholder="some@email.com" type="email"></b-input>
                                    </b-field>

                                    <b-field label="From" :label-position="label_position">
                                        <b-input class="mr-3" name="name" v-model="new_item.mail_fields.from.name" placeholder="Name" expanded></b-input>
                                        <b-input name="email" placeholder="some@email.com" v-model="new_item.mail_fields.from.email" type="email" expanded></b-input>
                                    </b-field>

                                    <b-field label="Subject" :label-position="label_position">
                                        <b-input placeholder="Subject" v-model="new_item.mail_fields.subject"></b-input>
                                    </b-field>

                                    <b-field label="Message Body" :label-position="label_position">
                                        <ContentFieldAll field_slug="editor"
                                                         label="Message"
                                                         placeholder="message"
                                                         :labelPosition="label_position"
                                                         v-model="new_item.mail_fields.message"
                                                         :upload_url="ajax_url+'/upload'">
                                        </ContentFieldAll>

                                    </b-field>
                                </div>
                            </div>
                        </section>
                    </b-tab-item>

                    <b-tab-item label="Messages">
                        <section>
                            <div class="mt-5">
                                <div class="block">

                                    <b-field label="Success" type="is-success" :label-position="label_position">
                                        <b-input v-model="new_item.message_fields.success"></b-input>
                                    </b-field>

                                    <b-field label="Failure" type="is-danger" :label-position="label_position">
                                        <b-input v-model="new_item.message_fields.failure"></b-input>
                                    </b-field>

                                    <b-field label="Terms" type="is-info" :label-position="label_position">
                                        <b-input v-model="new_item.message_fields.term"></b-input>
                                    </b-field>
                                </div>
                            </div>
                        </section>
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
