yii2-sms-ge-unicard
=====================

[![Build Status](https://travis-ci.org/alexeevdv/yii2-sms-ge-unicard.svg?branch=master)](https://travis-ci.org/alexeevdv/yii2-sms-ge-unicard) 
[![codecov](https://codecov.io/gh/alexeevdv/yii2-sms-ge-unicard/branch/master/graph/badge.svg)](https://codecov.io/gh/alexeevdv/yii2-sms-ge-unicard)
![PHP 5.6](https://img.shields.io/badge/PHP-5.6-green.svg)
![PHP 7.0](https://img.shields.io/badge/PHP-7.0-green.svg) 
![PHP 7.1](https://img.shields.io/badge/PHP-7.1-green.svg) 
![PHP 7.2](https://img.shields.io/badge/PHP-7.2-green.svg)


Yii2 wrapper for Geordian UNICARD sms provider

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require alexeevdv/yii2-sms-ge-unicard "~1.0.0"
```

or add

```
"alexeevdv/yii2-sms-ge-unicard": "~1.0.0"
```

to the ```require``` section of your `composer.json` file.

## Configuration

### Via application component
```php
'components' => [
    'sms' => [
        'class' => \alexeevdv\sms\ge\unicard\Provider::class,
        // If you want to ensure only Georgia phone numbers would be sent
        //'destinationChecker' => \alexeevdv\sms\ge\unicard\GeorgiaDestinationChecker::class,
    ],
],
```
## Usage

```php
Yii::$app
    ->sms
    ->compose('view', ['attribute' => 'value'])
    ->setTo('123412341234')
    ->setFrom('Sender') 
    ->send()
;
```

