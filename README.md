Loaf Settings
======

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