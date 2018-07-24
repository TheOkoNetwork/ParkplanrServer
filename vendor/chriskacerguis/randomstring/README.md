Randomstring
==========

[![Build Status](https://api.travis-ci.org/chriskacerguis/randomstring.png)](https://travis-ci.org/chriskacerguis/randomstring)

A simple library for creating a random string

### Requirments

* PHP 5.4+

### Usage

````

$random = new \chriskacerguis\Randomstring();

/*
Generate a random string of 8 charecters, using alpha numerics.
Result = string(8) "ab86d144"
*/
$str = $random->generate(8);

/*
Generate a random string of 15 charecters, using alpha numerics and special charecters
Result = string(15) "vT-`h9tG1Mt.Sj&"
*/
$str = $random->generate(15, true);

/*
Generate a random string of 13 charecters using alpha numerics, and wrap result with strtoupper()
Result = string(13) "LVFUK0X01HVD0"
*/
$str = $random->generate(13, false, 'strtoupper');

````

### Installation via [Composer](http://getcomposer.org/)

 * Install Composer to your project root:
    ```bash
    curl -sS https://getcomposer.org/installer | php
    ```

 * Add a `composer.json` file to your project:
    ```json
    {
      "require" {
        "chriskacerguis/randomstring": "1.0.*"
      }
    }
    ```

 * Run the Composer installer:
    ```bash
    php composer.phar install
    ```

### A Quick Note About Security

This library is built to be "enough" and fast for most applications (like generating an initial password, however 
for cryptographically secure applications, you *should* use the PHP method openssl_random_pseudo_bytes() or some
other method that uses a cryptographically strong algorithm.