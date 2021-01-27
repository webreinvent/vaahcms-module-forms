# vaahcms-module-forms
Forms Module for VaahCMS


#### To Run Modules Dusk Test:
- Change path of dusk in `phpunit.dusk.xml` to following:
```xml
...
<directory suffix="Test.php">./VaahCms/Modules/Forms/Tests/Browser</directory>
...
```

- Then run `php artisan dusk`