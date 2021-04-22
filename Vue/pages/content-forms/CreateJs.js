import GlobalComponents from '../../vaahvue/helpers/GlobalComponents'
import draggable from 'vuedraggable';

let namespace = 'content_forms';

export default {
    computed:{
        root() {return this.$store.getters['root/state']},
        page() {return this.$store.getters[namespace+'/state']},
        ajax_url() {return this.$store.getters[namespace+'/state'].ajax_url},
        new_item() {return this.$store.getters[namespace+'/state'].new_item},
    },
    components:{
        ...GlobalComponents,
        draggable,
    },
    data()
    {
        return {
            namespace: namespace,
            is_content_loading: false,
            is_btn_loading: null,
            labelPosition: 'on-border',
            params: {},
            local_action: null,
            title: null,
            new_status: null,
            disable_status_editing: true,
            edit_status_index: null,
            fields:[
                    {
                        id:1,
                        name:"First Name",
                        slug:"first_name",
                        is_required:false,
                        is_searchable:false,
                        excerpt:'Testing excerpt',
                        meta:{
                            is_hidden:false
                        },
                        type:{
                            name:"Text"
                        }
                    }
                ]
        }
    },
    watch: {
        $route(to, from) {
            this.updateView()
        },

        'new_item.name': {
            deep: true,
            handler(new_val, old_val) {

                if(new_val)
                {
                    this.new_item.slug = this.$vaah.strToSlug(new_val);
                    this.updateNewItem();
                }

            }
        },

        'new_item.plural': {
            deep: true,
            handler(new_val, old_val) {

                if(new_val)
                {
                    this.new_item.plural_slug = this.$vaah.strToSlug(new_val);
                    this.updateNewItem();
                }

            }
        },

        'new_item.singular': {
            deep: true,
            handler(new_val, old_val) {

                if(new_val)
                {
                    this.new_item.singular_slug = this.$vaah.strToSlug(new_val);
                    this.updateNewItem();
                }

            }
        },

    },
    mounted() {
        //----------------------------------------------------

        //----------------------------------------------------
        this.onLoad();
        //----------------------------------------------------

        //----------------------------------------------------
    },
    methods: {
        //---------------------------------------------------------------------
        //---------------------------------------------------------------------
        update: function(name, value)
        {
            let update = {
                state_name: name,
                state_value: value,
                namespace: this.namespace,
            };
            this.$vaah.updateState(update);
        },
        //---------------------------------------------------------------------
        updateNewItem: function()
        {
            let update = {
                state_name: 'new_item',
                state_value: this.new_item,
                namespace: this.namespace,
            };
            this.$vaah.updateState(update);
        },
        //---------------------------------------------------------------------
        updateView: function()
        {
            this.$store.dispatch(this.namespace+'/updateView', this.$route);
        },
        //---------------------------------------------------------------------
        onLoad: function()
        {
            this.is_content_loading = true;
            this.updateView();
            this.getAssets();
        },
        //---------------------------------------------------------------------
        async reloadRootAssets() {
            await this.$store.dispatch('root/reloadAssets');
        },
        //---------------------------------------------------------------------
        async getAssets() {
            await this.$store.dispatch(namespace+'/getAssets');
        },

        //---------------------------------------------------------------------
        addStatus: function()
        {
            this.new_item.content_statuses.push(this.new_status);
            this.new_status = null;
            this.update('new_item', this.new_item);
        },
        //---------------------------------------------------------------------
        toggleEditStatus: function(status_index)
        {
            this.edit_status_index = status_index;
            if(this.disable_status_editing)
            {
                this.disable_status_editing = false;
            } else
            {
                this.disable_status_editing = true;
            }
        },
        //---------------------------------------------------------------------
        create: function () {
            this.$Progress.start();
            let params = this.new_item;

            console.log('--->', params);

            let url = this.ajax_url+'/create';
            this.$vaah.ajax(url, params, this.createAfter);
        },
        //---------------------------------------------------------------------
        createAfter: function (data, res) {

            this.$Progress.finish();

            if(data)
            {
                this.$emit('eReloadList');

                if(this.local_action === 'save-and-close')
                {
                    this.saveAndClose();
                }else{
                    //this.$router.push({name: 'content.forms.list'});
                    this.saveAndNew();

                    // this.$root.$emit('eReloadItem');
                }

                this.reloadRootAssets();

            }

        },
        //---------------------------------------------------------------------
        setLocalAction: function (action) {
            this.local_action = action;
            this.store();
        },
        //---------------------------------------------------------------------
        saveAndClose: function () {
            this.update('active_item', null);
            this.$router.push({name:'content.forms.list'});
        },
        //---------------------------------------------------------------------
        saveAndNew: function () {
            this.update('active_item', null);
            this.resetNewItem();
        },
        //---------------------------------------------------------------------
        resetNewItem: function()
        {
            let new_item = this.getNewItem();
            this.update('new_item', new_item);
        },
        //---------------------------------------------------------------------
        getNewItem: function()
        {
            let new_item = {
                name: null,
                slug: null,
                plural: null,
                plural_slug: null,
                singular: null,
                singular_slug: null,
                excerpt: null,
                is_published: null,
                is_commentable: null,
                content_statuses: [
                    'draft',
                    'published',
                    'protected',
                ],
                meta: null,
            };
            return new_item;
        },
        //---------------------------------------------------------------------
        cloneField: function({ id, name, slug, meta })
        {

            let item = {
                name: null,
                slug: null,
                vh_cms_field_type_id: id,
                meta: meta,
                type: {
                    id: id,
                    name: name,
                    slug: slug,
                    meta: meta,
                }
            };

            console.log('--->cloned item', item);


            return item;


        },//---------------------------------------------------------------------
        toggleFieldOptions: function (event) {

            let el = event.target;

            console.log('--->', el);

            let target = $(el).closest('.dropzone-field').find('.dropzone-field-options');


            console.log('--->', target);
            target.toggle();

        },

        //---------------------------------------------------------------------
        deleteGroupField: function (fields, index) {
            fields.splice(index, 1);
            this.fields = fields;
        },
        //---------------------------------------------------------------------
    }
}
