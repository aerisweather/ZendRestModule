# ZendRestModule

A Zend module for creating RESTful web services.

- - -

## Installation

ZendRestModule can be installed via composer.

```sh
php composer.phar require aeris/zend-rest-module
```

Then add `'Aeris\ZendRestModule'` to the `'modules'` config in `/config/application.config.php`.

### Configuration Options

See /src/config/config.php.dist. More documentation to come...


## Features

ZendRestModule provides a number of features to facilitate creating a RESTful web service.

### Serialization using JMS Serializer

#### Serializer configuration Options

#### Additional Serializer Components

##### InitializedObjectConstructor

Allows you to deserialized data onto an existing enitity.

TODO: example using `update()`. See [this blog post](http://blog.edanschwartz.com/2014/09/29/zf2-partial-model-updates/).

##### IdenticalPropertyNamingStrategy

Fixes [bug which caused the original IdenticalPropertyNamingStrategy to ignore @SerializedName annotations](https://github.com/schmittjoh/serializer/issues/334).

##### DateTimeStampHandler

Serializes/deserializes between unix timestamps and `\DateTime` objects.

### JSON View Model Listener

The ZendRestModule listens to Controller return values, and converts raw data into JSON view models.

Here's how a RESTful controller might look without the ZendRestModule:

```php
class AnimalRestController extends AbstractRestfulController {
	public function get($id) {
    	$animal = $this
        	->animalRepository
            ->find($id);
        
        $jsonString = $this
        	->serviceManager
            ->get('jms_serializer')
            ->serialize($animal, 'json');

		// JsonModel expects an array, so we need
        // serialize the data back into a php array.
		$serializedData = json_decode($jsonString);
        
       
        // Alernatively, you could have serialized the
        // model by hand in the controller.
        // $serializedData = ['name' => $animal->getName() ] 
        // etc...
        
        return new JsonModel($serializedData);
    }
}
```

Using the ZendRestModule, you can simply return the raw model:

```php
class AnimalRestController extends AbstractRestfulController {
	public function get($id) {
    	return $this
        	->animalRepository
            ->find($id);
    }
}
```

The ZendRestModule will intercept the return value, convert the model to a `SerializedJsonModel`, then serialize the data according to your [JMS Serializer configuration](#serialization-using-jms-serializer).


### Exception Handling

ZendRestModule catches errors and exceptions thrown during the MVC event cycle, and converts the errors into JSON objects.

#### Example

```php
// zend-rest.config.php
'zend-rest' => [
	'errors' => [
    	[
          	'error' => \Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH,
        	'httpCode' => 404,
        	'applicationCode' => 'invalid_request',
        	'details' => 'The requested endpoint or action is invalid and not supported.',
        ],
        [
        	'error' => 'MyApp\Exception\RidiculousAnimalException',
            'httpCode' => 418,
            'applicationCode' => 'ridiculous_animal_error',
            'details' => 'The animal you have requested is too riduculous for our web service.',
            'onError' => function(RestErrorEvt $evt) {
            	error_log($evt->getError());
            }
        ],
        [
        	'error' => '\Exception',
            'httpCode' => 500,
            'applicationCode' => 'uh_oh',
            'details' => 'whoops!',
        ]
    ]
]


// AnimalRestController
class AnimalRestController extends AbstractRestfulController {
	public function get($id) {
    	if ($id === 'narwhal') {
        	throw new RidiculousAnimalException();
        }
        
        return $this->animalRepo->find($id);
    }
}
```

This example configures JSON output for errors occuring in the Animals Web Service. A request to `/animals/narwhal` would return a JSON object like:

```js
HTTP Code 418
{
	"error": {
    	"code": "ridiculous_animal_error",
        "details": "The animal you have requested is too riduculous for our web service."
    }
}
```

The error would also be written to the server log, by way of the `onError` callback.

Similarly a request to `/not/an/endpoint` would return a 404 error, with a `invalid_request` JSON application code.


### Serialization Groups

### AbstractTestCase

## Examples

For an example application using ZendRestModule, take a look at the `RestTestModule` fixture located in `/test/ZendRestModuleTest/Integration/Fixture/RestTestModule`.