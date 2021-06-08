
# Litebase Client Library for PHP #

The Litebase API Client Library enables you to work with Litebase APIs such as Charts, Drive, or Litebase Social on your server.

 These client libraries are officially supported by Broos Action. They are still considered under active development.
 Which means new features will be added as well as bug fix for current implemented client libraries.

## Requirements ##
* [PHP 7.4.0 or higher](http://www.php.net/)


## Installation ##

You can use **Composer** or simply **Download the Release**

### Composer

The preferred method is via [composer](https://getcomposer.org). Follow the
[installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have
composer installed.

Once composer is installed, execute the following command in your project root to install this library:

```sh
composer require broosaction/litebase-sdk-php:dev-master
```

Finally, be sure to include the autoloader:

```php
require_once '/path/to/your-project/vendor/autoload.php';
```


### Basic Example ###

```php
// include your composer dependencies
require_once 'vendor/autoload.php';

$litebaseclient = new LitebaseClient(array(
    'client_id' => 'api public key .x',
    'client_secret' => 'Api secret key .x',
    'username' => 'your litebase cloud username',
    'application_name' => 'your application name',
));


$qrcode = new \Litebase\Service\Charts\Qr_Code_Service($litebaseclient);
$qrcode->setText('Hello World')->setSize(300);
 if($qrcode->getStatus()){
    echo '<img src="'.$qrcode->getImageUrl().'"/>';
 }
```
