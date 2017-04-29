# Booster

Support classes.

### Installation

```bash
$ composer require gguney/boosters
```

### Preparation
Add package's service provider to your config/app.php

```php
...
        GGuney\Boosters\BoostersProvider::class,
...
```

Then write this line on cmd.
```bash
$ php artisan vendor:publish
```

It will publish 3 files to your resources/views/vendor/boosters folder. It will place these files in components folder.
'form.blade.php' is a view file that the package will use for form building. Create and Edit.
'show.blade.php' is a view file that the package will use for Item show.
'table.blade.php' is a view file that the package will use for Index page.

Also, boosters.php into the config folder. With this config file you can change views' place.

All views also support language. First, you can select language in config/app.php
```php
...
    'locale' => 'en',
...
```
And inside resources/lang/your_locale_name create general.php
```php
<?php
return [
    'Create' => 'Ekle',
    'Edit' => 'Düzenle',
    'Update' => 'Güncelle',
    'Delete' => 'Sil',
    'Show' => 'Göster',
    'Users' => 'Kullanıcılar',
];
?>
```
This way package will translate the words to you language.
Also, You have to add few lines to you app.blade.php so package can work fully operational.

```php
...
<script
  src="https://code.jquery.com/jquery-3.1.1.min.js"
  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
  crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.13/css/jquery.dataTables.css">
<script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
...

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
...
```

### Usage

```bash
$ php artisan make:boostedController YourModelName --g
```

YourModelName variable is the name of your model.
--g (optional) option is general controller option.

This command will create a controller with booster abilities. If you don't need special changes for your model you can create just 1 general controller and route all these models to this general controller.

### Author

Gökhan Güney - <gokhanguneygg@gmail.com><br />

### License

Boosters is licensed under the MIT License - see the `LICENSE` file for details
