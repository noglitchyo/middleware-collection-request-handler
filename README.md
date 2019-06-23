# middleware-collection-request-handler

Lightweight & simple PSR-15 server request handler implementation to handle middleware collection.

![PHP from Packagist](https://img.shields.io/packagist/php-v/noglitchyo/middleware-collection-request-handler.svg)
[![Build Status](https://travis-ci.org/noglitchyo/middleware-collection-request-handler.svg?branch=master)](https://travis-ci.org/noglitchyo/middleware-collection-request-handler)
[![codecov](https://codecov.io/gh/noglitchyo/middleware-collection-request-handler/branch/master/graph/badge.svg)](https://codecov.io/gh/noglitchyo/middleware-collection-request-handler)
![Scrutinizer code quality (GitHub/Bitbucket)](https://img.shields.io/scrutinizer/quality/g/noglitchyo/middleware-collection-request-handler.svg)
![Packagist](https://img.shields.io/packagist/l/noglitchyo/middleware-collection-request-handler.svg)

### Description

Request handler implementing the [RequestHandlerInterface](https://github.com/php-fig/http-server-handler/blob/master/src/RequestHandlerInterface.php) 
and able to manage a collection of middlewares implementing the [MiddlewareInterface](https://github.com/php-fig/http-server-middleware/blob/master/src/MiddlewareInterface.php).

This middleware attempts to provide interoperability to process a collection of middlewares and
give the possibility to define the strategy on how middlewares are fetched.

### Getting started

#### Installation

`composer require noglitchyo/middleware-collection-request-handler`

#### Run

The RequestHandler class only need 2 arguments:

- A default request handler (implementing [RequestHandlerInterface](https://github.com/php-fig/http-server-handler/blob/master/src/RequestHandlerInterface.php))

It is there to provide the response if no middleware created one.
For example, you are using ADR pattern, the default request handler might be your Action.
It is also possible to directly provide a `callable` using `RequestHandler::fromCallable()`. 
It will generate a generic instance of RequestHandlerInterface wrapping the `callable` inside.

***Remember: the default request handler is responsible for providing a default response.***

- A middleware collection (implementing [MiddlewareCollectionInterface](https://github.com/noglitchyo/middleware-collection-request-handler/blob/master/src/MiddlewareCollectionInterface.php))

It defines how you store your middlewares and the strategy to retrieve them.
Some implementations are provided by default and will probably fit your needs.
They offers different strategies varying from the Stack to the Queue.

However, it is really simple to create you own collection if needed, since MiddlewareCollectionInterface requires only 3 methods.

#### Tests

`composer test`

### References

https://www.php-fig.org/psr/psr-15/

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
