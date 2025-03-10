# Requirements

- Php 8.2.0
- Composer
- Mysql
- Apache/Nginx

## Installation and Usage

Clone the git repository

Install and Update composer dependencies

```bash
composer install

npm install

cp .env.example .env

php artisan key:generate

php artisan storage:link

php artisan migrate:fresh --seed

npm run dev / npm run build

php artisan serve
```

and final touch is optimize the config to apply the change by running this script:

```
php artisan optimize
```

create new module with common

```
php artisan common:generate News
```

load translate

```
php artisan lang:update
```

## Login With

### Superuser

```bash
email : superuser@company.com
password : p4ssw0rd##
```

### Participant

```bash
email : participant@company.com
password : p4ssw0rd##
```

# Built With

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
