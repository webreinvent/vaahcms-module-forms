<?php

return [
    "name"=> "Forms",
    "title"=> "Module to create Forms",
    "slug"=> "forms",
    "thumbnail"=> "https://placehold.jp/300x160.png",
    "excerpt"=> "Create Forms",
    "description"=> "Create Forms",
    "download_link"=> "",
    "author_name"=> "forms",
    "author_website"=> "https://vaah.dev",
    "version"=> "0.0.1",
    "is_migratable"=> true,
    "is_sample_data_available"=> true,
    "db_table_prefix"=> "vh_forms_",
    "providers"=> [
        "\\VaahCms\\Modules\\Forms\\Providers\\FormsServiceProvider"
    ],
    "aside-menu-order"=> null
];
