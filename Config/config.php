<?php

return [
    "name"=> "Form",
    "title"=> "Module to create Form",
    "slug"=> "form",
    "thumbnail"=> "https://placehold.jp/300x160.png",
    "excerpt"=> "Create Form",
    "description"=> "Create Form",
    "download_link"=> "",
    "author_name"=> "form",
    "author_website"=> "https://vaah.dev",
    "version"=> "0.0.1",
    "is_migratable"=> true,
    "is_sample_data_available"=> true,
    "db_table_prefix"=> "vh_form_",
    "providers"=> [
        "\\VaahCms\\Modules\\Form\\Providers\\FormsServiceProvider"
    ],
    "aside-menu-order"=> null
];
