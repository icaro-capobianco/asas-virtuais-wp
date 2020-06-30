# Asas Virtuais WP Framework

## Asas Virtuais means Virtual Wings.

## Goals
- Gather code that is very common among Wordpress plugins.

## Benefits
- Eliminates the practice of copying and pasting the same classes and methods from one plugin to the other.
- Keeps **only** what is specific to each plugin in the plugin.

<hr>

## Requiring the Framework:

```shell
composer require asas-virtuais/asas-virtuais-wp
```

<hr>

## Loading the Framework:
**Make sure dependencies were properly installed and the framework is under vendor**

1 - Require TakeOff.php
```php
$loader = require_once( plugin_dir_path( __FILE__ ) . 'vendor/asas-virtuais/asas-virtuais-wp/TakeOff.php' );
```
2 - Call the fly method, passing the composer autoload and the plugin file path.
 - It is better to do this under the plugins_loaded hook
```php
$autoload = require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );
$framework_instance = $loader->fly( $autoload, __FILE__ );
```
<hr>

## Using the Framework

Once the fly method was called you can call for any function in the files under the lib directory, and the framework instance can be retrieved like in the examples below:

```php
$framework_instnace = asas_virtuais( 'my-plugin-name' );
```
- my-plugin-name is always the basename of the main plugin file, without the extension of course.


The framework also has a default instance which can be used in certain cases.
```php
$framework_default_instance = asas_virtuais();
```

## Using the Manager classes
***Beware of the args of manager constructors and the inner workings of each manager, some will require input to be instantiated and others will require that another manager is instantiated first.***

***Managers are not instantiated automatically, they will be instantiated as you use them.***

The manager classes can be either extended or called directly from the framework instance.

Extending the a Manager
```php
class MyAssetsManager extends \AsasVirtuaisWP\Assets\AssetsManager
```

The manager classes can be either extended or called directly from the framework instance.

```php
$my_assets_manager = asas_virtuais('my-plugin-name')->assets_manager( $args );
```

### Best Practices
 - Only call the ```$loader->fly``` under the plugins_loaded hook.
 - Save your framework instance in a class variable instead of calling ```asas_virtuais``` multiple times.
 - Save your manager instances in a class variable instead of relying on the ```asas_virtuais()->x_manager();``` methods multiple times.


<hr>

## What happens behind the hood

1 - When you require TakeOff.php
- The class \AsasVirtuais\WP\Framework\TakeOff is instantiated **if it hasn't been already**. This class is only instnatiated once, regardless of how many plugins use the framework.
- TakeOff will register all the plugins that required the framework but will only load the latest version of the framework.

2 - When you call the method TakeOff->fly
- This will trigger the framework loading, assuming all plugins required TakeOff.php before the plugins_loaded hook and only called the Fly method after the plugins_loaded hook, the latest version available will be loaded.
- If a plugin calls the fly method before the plugins_loaded hook, the framework version on that plugin will be used regardless of other plugins having a more recent version of the framework.

<hr>

## Problems and Solutions

 - The main problem we face is having the framework availalbe for multiple plugins but only loading it once. This problem is currently solved by using the TakeOff class/file to load the latest version of the framework registered.
