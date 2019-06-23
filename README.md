# middleware-collection-request-handler

A simple PSR-15 server request handler implementation to handle middleware collection.

![PHP from Packagist](https://img.shields.io/packagist/php-v/noglitchyo/middleware-collection-request-handler.svg)
[![Build Status](https://travis-ci.org/noglitchyo/middleware-collection-request-handler.svg?branch=master)](https://travis-ci.org/noglitchyo/middleware-collection-request-handler)
[![codecov](https://codecov.io/gh/noglitchyo/middleware-collection-request-handler/branch/master/graph/badge.svg)](https://codecov.io/gh/noglitchyo/middleware-collection-request-handler)
![Scrutinizer code quality (GitHub/Bitbucket)](https://img.shields.io/scrutinizer/quality/g/noglitchyo/middleware-collection-request-handler.svg)
![Packagist](https://img.shields.io/packagist/l/noglitchyo/middleware-collection-request-handler.svg)

### Description

Request handler implementing the [RequestHandlerInterface](https://github.com/php-fig/http-server-handler/blob/master/src/RequestHandlerInterface.php) 
and able to manage a collection of middlewares implementing the [MiddlewareInterface](https://github.com/php-fig/http-server-middleware/blob/master/src/MiddlewareInterface.php).

This middleware attempts to provide interoperability to process a collection middlewares and
offer the possibility to define the strategy on how middlewares are fetched.

### Getting started

#### Installation

`composer require noglitchyo/middleware-collection-request-handler`

#### Tests

`composer test`

### References

https://www.php-fig.org/psr/psr-15/
