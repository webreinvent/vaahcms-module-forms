let routes=[];
let routes_list=[];


//----------Middleware
import GetAssets from './middleware/GetAssets'
//----------Middleware


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

import Backend from './../layouts/Backend'
import Index from './../pages/dashboard/Index'

routes_list =     {
    path: '/',
    component: Backend,
    props: true,
    meta: {
        middleware: [
            GetAssets
        ]
    },
    children: [
        {
            path: '/',
            name: 'form.index',
            component: Index,
            props: true,
            meta: {
                middleware: [
                    GetAssets
                ]
            },
        },

    ]
};

routes.push(routes_list);




import ContentFormList from "./../pages/content-forms/List";
import ContentFormCreate from "./../pages/content-forms/Create";
import ContentFormView from "./../pages/content-forms/View";
import ContentFormContentStructure from "./../pages/content-forms/ContentStructure";
import ContentFormEdit from "./../pages/content-forms/Edit";

routes_list =     {
    path: '/content-forms',
    component: Backend,
    props: true,
    meta: {
        middleware: [
            GetAssets
        ]
    },
    children: [
        {
            path: '/',
            name: 'content.forms.list',
            component: ContentFormList,
            props: true,
            meta: {
                middleware: [
                    GetAssets
                ]
            },
            children: [
                {
                    path: 'create',
                    name: 'content.forms.create',
                    component: ContentFormCreate,
                    props: true,
                    meta: {
                        middleware: [
                            GetAssets
                        ]
                    },
                },
                {
                    path: 'content-structure/:id',
                    name: 'content.forms.content.structure',
                    component: ContentFormContentStructure,
                    props: true,
                    meta: {
                        middleware: [
                            GetAssets
                        ]
                    },
                },
                {
                    path: 'view/:id',
                    name: 'content.forms.view',
                    component: ContentFormView,
                    props: true,
                    meta: {
                        middleware: [
                            GetAssets
                        ]
                    },
                },
                {
                    path: 'edit/:id',
                    name: 'content.forms.edit',
                    component: ContentFormEdit,
                    props: true,
                    meta: {
                        middleware: [
                            GetAssets
                        ]
                    },
                }

            ]
        }

    ]
};

routes.push(routes_list);


/*
|--------------------------------------------------------------------------
| Content Types Routes
|--------------------------------------------------------------------------
*/


export default routes;
