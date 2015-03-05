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
