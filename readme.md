[![Build Status](http://img.shields.io/travis/ayeo/parser.svg?style=flat-square)](https://travis-ci.org/ayeo/parser)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/ayeo/parser.svg?style=flat-square)](https://scrutinizer-ci.com/g/ayeo/parser/build-status/master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](license.md)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ayeo/parser/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/ayeo/parser/?branch=master)

# Simple placeholder parser

Parser replaces specific placeholders with proper data. 

Basic usage
===========

Let's take a look at the simplest possible example:

```php
$parser = new Parser;
$string = "Hello {{name}}!";
$parser->parse($string, ['name' => 'Nikola Tesla']); //returns: Hello Nikola Tesla!
```

Of course you may use object instead of primitive array

```php
$parser = new Parser;

$customer = new Customer;
$customer->name = 'Nikola Tesla';

$string = "Hello {{customer.name}}!";
$parser->parse($string, ['customer' => $customer]); //returns: Hello Nikola Tesla!
```

Parser is smart enough to access private properties using getters.

It also supports nested objects as well

```php
$parser = new Parser;

$customer = new Customer;
$address = new Address('Green Alley', 12, 'London', 'LN4 4GD', 'United Kingdom');
$customer->setAddress($address);

$string = "{{customer.address.street}}";
$parser->parse($string, ['customer' => $customer]); //returns: Green Alley
```

Nested array is also welcome

```php
$parser = new Parser();
$data = ['user' =>  ['supervisor' => ['name' => 'Harry']]];
$parser->parse('Hi {{user.supervisor.name}}!', $data); //returns: Hi Harry!
```

Formatting
==========

Sometimes you need to use additional formatting for specific objects. The great example is DateTime. The best way to 
achieve this goal is use adapter pattern (or decorator) for your object. The parser shouldnt be able to do that for you.
