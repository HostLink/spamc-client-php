PHP Client for SPAMC
=====

This library was fork from [https://github.com/licor/php-spamc-client]

## Installation

```
composer require hostlink/spamc-client-php
```


## Usage

```php
use Spamc\Client;
$client=new Client();
$report=$client->getSpamcReport('This is a spam message');
```
