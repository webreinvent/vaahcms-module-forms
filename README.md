# vaahcms-module-cms
Forms Module for VaahCMS

#### New Version Release
- Version should be updated in `composer.json` and `Config/config.php` file


#### For Hot Reload add following:
```dotenv
APP_MODULE_FORM_ENV=develop
```



#### Code to paste
```blade
{!! vh_form('contact') !!}
```


#### To Run Modules Dusk Test:
- Change path of dusk in `phpunit.dusk.xml` to following:
```xml

<directory suffix="Test.php">./VaahCms/Modules/Form/Tests/Browser</directory>

```

- Then run `php artisan dusk`




### Change Log
- Install `npm install auto-changelog -g`
- To generate `CHANGELOG.md`, run `auto-changelog` in the root folder of the project

Maintain following pre-fixes to your commit messages:
```md
Added:
Changed:
Deprecated:
Removed:
Fixed:
Security:
```
