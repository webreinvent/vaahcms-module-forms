import GlobalComponents from '../../vaahvue/helpers/GlobalComponents'
import draggable from 'vuedraggable';
import ContentFieldAll from '../../vaahvue/reusable/content-fields/All'

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
        ContentFieldAll,
        draggable,
    },
    data()
    {
        return {
            namespace: namespace,
            is_content_loading: false,
            copyright_text_custom: false,
            is_btn_loading: null,
            label_position: 'on-border',
            params: {},
            local_action: null,
            url_type: 'internal',
            title: null,
            new_status: null,
            disable_status_editing: true,
            edit_status_index: null
        }
    },
    watch: {
        $route(to, from) {
            this.updateView();
            this.updateMessages();
        },

        'page.assets': {
            deep: true,
            handler(new_val, old_val) {

                if(new_val)
                {
                    this.updateMessages();
                    this.setMailCredential();
                }

            }
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
        setMailCredential: function()
        {
            if(this.page.assets && this.page.assets.mail){
                this.new_item.mail_fields.to = this.page.assets.mail.address;
                this.new_item.mail_fields.from.email = this.page.assets.mail.address;
                this.new_item.mail_fields.from.name = this.page.assets.mail.name;
            }
            this.update('new_item', this.new_item);
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
                is_published: null,
                action_url: null,
                method_type: 'POST',
                is_use_default_url: true,
                fields:[],
                mail_fields:{
                    to:null,
                    from:{
                        name:null,
                        email:null
                    },
                    subject:null,
                    additional_header:null,
                    message:null
                },
                message_fields:JSON.parse(JSON.stringify(this.page.assets.form_messages))
            };
            return new_item;
        },
        //---------------------------------------------------------------------
        cloneField: function({ id, name, slug, meta })
        {

            let item = {
                name: null,
                slug: null,
                vh_form_field_type_id: id,
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
        deleteGroupField: function (new_item, index) {
            new_item.fields.splice(index, 1);
           this.update('new_item',new_item);
        },

        //---------------------------------------------------------------------
        updateMessages : function (){
            if(this.page.assets.form_messages){
                this.new_item.message_fields = JSON.parse(JSON.stringify(this.page.assets.form_messages));
                this.update('new_item',this.new_item);
            }

        },
        //---------------------------------------------------------------------
    }
}
