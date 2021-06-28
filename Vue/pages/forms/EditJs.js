import GlobalComponents from '../../vaahvue/helpers/GlobalComponents'
import draggable from 'vuedraggable';
import ContentFieldAll from '../../vaahvue/reusable/content-fields/All'

let namespace = 'content_forms';


export default {
    props: ['id'],
    computed:{
        root() {return this.$store.getters['root/state']},
        page() {return this.$store.getters[namespace+'/state']},
        ajax_url() {return this.$store.getters[namespace+'/state'].ajax_url},
        item() {return this.$store.getters[namespace+'/state'].active_item},
    },
    components:{
        ...GlobalComponents,
        ContentFieldAll,
        draggable

    },
    data()
    {
        return {
            namespace: namespace,
            is_content_loading: false,
            is_btn_loading: null,
            label_position: 'on-border',
            params: {},
            url_type: 'internal',
            local_action: null,
            title: null,
            edit_status_index: null,
            status: null,
            disable_status_editing: true,
        }
    },
    watch: {
        $route(to, from) {
            this.onLoad()
        },

        'item.name': {
            deep: true,
            handler(new_val, old_val) {

                if(new_val)
                {
                    this.item.slug = this.$vaah.strToSlug(new_val);
                    this.updateNewItem();
                }

            }
        },

        'item.plural': {
            deep: true,
            handler(new_val, old_val) {

                if(new_val)
                {
                    this.item.plural_slug = this.$vaah.strToSlug(new_val);
                    this.updateNewItem();
                }

            }
        },

        'item.singular': {
            deep: true,
            handler(new_val, old_val) {

                if(new_val)
                {
                    this.item.singular_slug = this.$vaah.strToSlug(new_val);
                    this.updateNewItem();
                }

            }
        },

    },
    mounted() {
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
        updateView: function()
        {
            this.$store.dispatch(this.namespace+'/updateView', this.$route);
        },
        //---------------------------------------------------------------------
        updateNewItem: function()
        {
            let update = {
                state_name: 'item',
                state_value: this.item,
                namespace: this.namespace,
            };
            this.$vaah.updateState(update);
        },
        //---------------------------------------------------------------------
        onLoad: function()
        {
            this.is_content_loading = true;

            this.updateView();
            this.getAssets();
            this.getItem();
        },
        //---------------------------------------------------------------------
        async getAssets() {
            await this.$store.dispatch(namespace+'/getAssets');
        },
        //---------------------------------------------------------------------
        getItem: function () {
            this.$Progress.start();
            this.params = {};
            let url = this.ajax_url+'/item/'+this.$route.params.id;
            this.$vaah.ajaxGet(url, this.params, this.getItemAfter);
        },
        //---------------------------------------------------------------------
        getItemAfter: function (data, res) {
            this.$Progress.finish();
            this.is_content_loading = false;

            if(data)
            {
                this.title = data.name;

                if(data.action_url &&
                    (data.action_url.includes('https://')
                        || data.action_url.includes('http://'))){
                    this.url_type = 'external';
                }

                this.update('active_item', data);
            } else
            {
                //if item does not exist or delete then redirect to list
                this.update('active_item', null);
                this.$router.push({name: 'content.forms.list'});
            }
        },
        //---------------------------------------------------------------------
        store: function () {
            this.$Progress.start();

            let params =  this.item;

            let url = this.ajax_url+'/store/'+this.item.id;
            this.$vaah.ajax(url, params, this.storeAfter);
        },
        //---------------------------------------------------------------------
        storeAfter: function (data, res) {

            this.$Progress.finish();

            if(data)
            {
                this.$emit('eReloadList');

                if(this.local_action === 'save-and-close')
                {
                    this.saveAndClose()
                }else{
                    this.getItem();
                }

                this.reloadRootAssets();

            }

        },
        //---------------------------------------------------------------------
        async reloadRootAssets() {
            await this.$store.dispatch('root/reloadAssets');
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

        addStatus: function()
        {
            this.item.content_statuses.push(this.status);
            this.status = null;
            this.update('item', this.item);
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
        deleteGroupField: function (item, index) {
            item.fields.splice(index, 1);
            this.update('active_item',item);
        },
    }
}
