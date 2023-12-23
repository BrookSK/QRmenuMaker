# FoodTiger | QR SaaS | WhatsApp | LionPOS

[![FT](https://i.imgur.com/gcgJEb2.jpg)](https://codecanyon.net/user/mobidonia/portfolio)
[![QR](https://i.imgur.com/bqpWgnU.jpg)](https://codecanyon.net/user/mobidonia/portfolio)
[![WP](https://i.imgur.com/VgHDizv.jpg)](https://codecanyon.net/user/mobidonia/portfolio)


## Test
sail artisan test --testsuite=Feature

## License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## ENV
SHOW_DEMO_CREDENTIALS=true
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel


MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS='test@example.com'
MAIL_FROM_NAME='App Demo'

## Updates

git diff --name-only 49b736c231b1de9ae4ecdce31307960a9d13b087 0abba369fa214151892283d938f8e58dacabd592 > .diff-files.txt && npm run zipupdate

COMPOSER_MEMORY_LIMIT=-1 composer require */**

## Clearing cache
sail artisan cache:clear
ddcache
sail artisan config:cache
sail artisan config:clear
sail artisan route:clear
sail artisan config:cache
sail artisan route:cache
sail artisan optimize

## Create new module
sail artisan module:make Fields
sail artisan module:make-migration create_fields_table fields
https://github.com/akaunting/laravel-module

## Zip withoit mac
zip -r es_lang.zip . -x ".*" -x "__MACOSX"

## Sync missing keys
sail artisan translation:sync-missing-translation-keys


## Default .env
[.env](https://paste.laravel.io/2fe670c7-f66b-443e-9e79-b5fa6618360b)