# v1.1.5
* add support for ZF2.5
* only apply listeners if JSON call
* readme (config) fixes

# v1.1.4

* Attempt to reduce memory leakage in `AbstractTestCase`

# v1.1.3

* Fix error handling, when the result object is not a ViewModel

# v1.1.2

* Removed dev dependencies.

# v1.1.1

* Setting a 'serializer'.'handlers' config does not override the
  default ZendRestModule handlers (currently DateTimeTimestamp)

# v1.1.0

* Serializer accepts `subscribers` option
* Serializer accepts `listeners` options

# v1.0.0

Initial release.

* Serializer Service
* Automatic serialization of controller entity responses
* JMS Serializer extensions
  - IdenticalPropertyNamingStrategy (fixes bug in original)
  - InitializedObjectConstructor
  - DateTimeTimestampHandler type
* Serialization groups configuration and @View\Groups() annotation
* RESTful exception handling
* AbstractTestCase
