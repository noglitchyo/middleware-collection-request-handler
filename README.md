# middleware-collection-request-handler

Lightweight & dead simple PSR-15 Server Request Handler implementation to process a collection of middlewares.

![PHP from Packagist](https://img.shields.io/packagist/php-v/noglitchyo/middleware-collection-request-handler.svg)
[![Build Status](https://travis-ci.org/noglitchyo/middleware-collection-request-handler.svg?branch=master)](https://travis-ci.org/noglitchyo/middleware-collection-request-handler)
[![codecov](https://codecov.io/gh/noglitchyo/middleware-collection-request-handler/branch/master/graph/badge.svg)](https://codecov.io/gh/noglitchyo/middleware-collection-request-handler)
![Scrutinizer code quality (GitHub/Bitbucket)](https://img.shields.io/scrutinizer/quality/g/noglitchyo/middleware-collection-request-handler.svg)
![Packagist](https://img.shields.io/packagist/l/noglitchyo/middleware-collection-request-handler.svg)

### Description

PSR-7 Request Handler implementing the [RequestHandlerInterface](https://github.com/php-fig/http-server-handler/blob/master/src/RequestHandlerInterface.php) 
and able to manage a collection of Middlewares implementing the [MiddlewareInterface](https://github.com/php-fig/http-server-middleware/blob/master/src/MiddlewareInterface.php).

It comes with a set of middleware collections using different strategy on how to provide the middlewares to the RequestHandler, and also provide a dead simple collection interface to implement in a glimpse your own strategy.

### Goals

- Simplicity
- Interoperability

### Getting started

#### Requirements

- PHP >= 7.3

#### Installation

`composer require noglitchyo/middleware-collection-request-handler`

#### Run

Instantiate the RequestHandler class. It requires ony 2 arguments: 

- `$defaultRequestHandler` : [RequestHandlerInterface](https://github.com/php-fig/http-server-handler/blob/master/src/RequestHandlerInterface.php)

***The default request handler MUST provide a default response if none of the middlewares created one.***

Some examples of what could be a "default request handler": 
- with the [ADR pattern](https://en.wikipedia.org/wiki/Action%E2%80%93domain%E2%80%93responder), the default request handler might be your action class.
- with the [MVC pattern](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller), the default request handler might be the action method of your controller.

It is possible to directly provide a `callable` and use the factory method `RequestHandler::fromCallable(callable $callable)`. 
It will create an anonymous instance of RequestHandlerInterface wrapping the given `callable` inside.

- `$middlewareCollection` : [MiddlewareCollectionInterface](https://github.com/noglitchyo/middleware-collection-request-handler/blob/master/src/MiddlewareCollectionInterface.php)

Contains the middlewares and defines the strategy used to store the middlewares and to retrieve the next middleware.
Some implementations with common strategies are provided: [stack (LIFO)](https://github.com/noglitchyo/middleware-collection-request-handler/blob/master/src/Collection/SplStackMiddlewareCollection.php), [queue (FIFO)](https://github.com/noglitchyo/middleware-collection-request-handler/blob/master/src/Collection/SplQueueMiddlewareCollection.php).

##### Example

Below, this is how simple it is to get the middleware handler running:

```php
<?php
use NoGlitchYo\MiddlewareCollectionRequestHandler\RequestHandler;
use NoGlitchYo\MiddlewareCollectionRequestHandler\Collection\SplStackMiddlewareCollection;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

// Instantiate a collection of middlewares with an anonymous middleware class.
// In this example, we are using a "stack" implementation of the collection.
$middlewareCollection = new SplStackMiddlewareCollection([
    new class implements MiddlewareInterface{
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface{
            return $handler->handle($request);
        }
    }
]);

// Let's add one more middleware in the collection (this time, from a callable)
$middlewareCollection->addFromCallable(function(ServerRequestInterface $request, RequestHandlerInterface $handler){
    return $handler->handle($request);
});

// Instantiate a new request handler with a default handler and the middleware collection.
$requestHandler = RequestHandler::fromCallable(
    function (ServerRequestInterface $serverRequest){
        return new /* instance of ResponseInterface */;
    }, 
    $middlewareCollection
);

// Pass the request to the request handler which will dispatch the request to the middlewares.
$response = $requestHandler->handle(/* ServerRequestInterface */); 

```

#### Create a custom MiddlewareCollectionInterface implementation

It is easy to create a new MiddlewareCollectionInterface implementation if needed. The interface requires only 3 methods:
```php
<?php
interface MiddlewareCollectionInterface
{
    /**
     * Must return true if there is no middleware in the collection to process.
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Must return the next middleware to process in the collection.
     * Depending on the implemented strategy, the middleware MAY not be removed from the collection.
     * @return MiddlewareInterface
     */
    public function next(): MiddlewareInterface;

    /**
     * Add a middleware instance of MiddlewareInterface to the collection.
     *
     * @param MiddlewareInterface $middleware
     *
     * @return MiddlewareCollectionInterface
     */
    public function add(MiddlewareInterface $middleware): MiddlewareCollectionInterface;
}
```

#### Tests

Would like to the run the test suite? Go ahead:

`composer test`

### References

https://www.php-fig.org/psr/psr-15/

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
