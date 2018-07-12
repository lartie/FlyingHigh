# FlyingHigh Telegram Bot

## Зависимости Проекта

### ПО

* php 7.0.5 (sudo apt-get install php7.0 php7.0-fpm php7.0-mysql -y)
* mysql 5.7.13
* supervisor (sudo apt-get install supervisor)
* beanstalkd 1.10 (sudo apt-get install beanstalkd)

### PHP Расширения

* zip (sudo apt-get install php7.0-zip)
* PDO
* MCrypt
* OpenSSL
* Mbstring
* Tokenizer

## Установка

```sh
composer install

php artisan vendor:publish

php artisan queue:table
php artisan queue:failed-table

php artisan migrate

php artisan airports:install
```

## Настройка

1. Создать .env файл по образцу из .env.example

2. Cron задачи находятся в storage/app/cron

3. Supervisor задачи находятся в storage/app/supervisor

## Заметки

* Команды и токен бота находятся в конфиге flyinghigh.php

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
