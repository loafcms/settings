Loaf Settings
======

![Build Status](https://build.webhub.nl/buildStatus/icon?job=loaf-settings)

User-customizable settings for Loaf. 

Usage
-----

The package provides a facade for easy access to settings. 

```php
// Set a value
Settings::set('website.appearance.color', '#dbedff');

// Get a value
$value = Settings::get('website.appearance.color', $default_value);
```

Sections, Groups, Fields, Types and Models
-----

Settings are categorized in `sections` that contain `groups` that contain `fields`. 
`fields` have a specific `SettingType` (`type`) with a corresponding database `model`. 