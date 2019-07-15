# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.1] - 2019-07-15
### Fixed
- When RequestHandler was used as a middleware, default RequestHandler was not saved and wrong default request handler 
was called.

## [2.0.0] - 2019-07-12
### Added
- RequestHandler supports MiddlewareInterface to be use as a middleware as well

### Changed
- Constructor arguments order for RequestHandler
- Default handler is now optional

## [1.1.0] - 2019-06-26
### Added
- Handful factory methods
- Updated documentation

## [1.0.0] - 2019-06-23
- Initial release
