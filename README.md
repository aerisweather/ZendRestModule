# ZendRestModule

A Zend Framework 2 module to help you create RESTful web services.

- - -

## Installation

ZendRestModule can be installed via composer.

```sh
php composer.phar require aeris/zend-rest-module
```

Then add `'Aeris\ZendRestModule'` to the `'modules'` config in `/config/application.config.php`.




## Features

ZendRestModule provides a number of features to facilitate creating a RESTful web service.

* Entity serialization
* RESTful Exception handling
* Test Helpers

### Serializer

ZendRestModule uses the popular [JMS Serializer](http://jmsyst.com/libs/serializer) to serialize and deserialize application models. 

The JMS Serializer allows you to use [Annotations](http://jmsyst.com/libs/serializer/master/reference/annotations), [XML](http://jmsyst.com/libs/serializer/master/reference/xml_reference), or [YML](http://jmsyst.com/libs/serializer/master/reference/yml_reference) to  configure how objects are serialized and deserialized from raw data.

While you can use the JMS serializer on its own, ZendRestModule introduces behaviors to make serialization/deserialization a lot easier in the context of Zend APIs.

Here's how a RESTful controller might look *without* the ZendRestModule:

#### Automatic Enitity Serialization

```php
class AnimalRestController extends AbstractRestfulController {
	public function get($id) {
    	// Grab the animal entity from the database
    	$animal = $this
        	->animalRepository
            ->find($id);
        
        // Serialize the animal into a JSON string
        $jsonString = $this
        	->serviceManager
            ->get('the_jms_serializer_service_I_configured_on_my_own')
            ->serialize($animal, 'json');

		// But JsonModel expects an array, so we need
        // deserialize the JSON string back into a php array.
		$serializedData = json_decode($jsonString);
        
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

#### Deserialize onto Existing Entities

Out of the box, the JMS Serializer allows you to deserialize raw data into entity objects. 

```php
class AnimalRestController extends AbstractRestfulController {
	public function create($data) {
    	// Deserialize the raw JSON data into 
        // a MyApp\Entity\Animal object
    	$animal = $this->serviceManager
            ->get('Aeris\ZendRestModule\Serializer')
            ->deserialize($data, 'MyApp\Entity\Animal');
        
        $this->saveToDb($animal);
        
        return $animal;
    }
}
```

ZendRestModule includes a [JMS object constructor extension](https://github.com/aerisweather/ZendRestModule/blob/development/src/Service/Serializer/Constructor/InitializedObjectConstructor.php), which allows you to deserialize data onto an existing entity. This is very useful for PUT requests, where the request data my not be a fully defined entity.

```php
class AnimalRestController extends AbstractRestfulController {
	public function update($id, $data) {
    	$animal = $this->entityManager
        	->getRepo('MyApp\Entity\Animal')
        	->find($id);
        
        // Update the existing animal from the
        // PUT data
        $this->serviceManager
            ->get('Aeris\ZendRestModule\Serializer')
            ->deserialize($data, $animal);
            
        $this->saveToDb($animal);
        
        return $animal;
   	} 
   
}
```


See [this article](http://blog.edanschwartz.com/2014/09/29/zf2-partial-model-updates/) for a long rant about how and why this works.



#### Serialization Groups

The JMS Serializer allows you to configure serialization groups for entities. 
This is useful for setting what data different request return. For example, I may only want to see an animal's id and name in a `getList` response, but see more details in a `get` response:

```php
use JMS\Serializer\Annotation as JMS;

class Animal {
	/**
	 * @JMS\Groups({"animalSummary", "animalDetails"})
	 * @var int
	 */
	public $id;

	/**
	 * @JMS\Groups({"animalSummary", "animalDetails"})
	 * @var string
	 */
	public $name;

	/**
	 * @JMS\Groups({"animalDetails"})
	 * @var string
	 */
	public $species;

	/**
	 * @JMS\Groups({"animalDetails"})
	 * @var string
	 */
	public $color;

	/**
	 * @JMS\Groups({"dates"})
	 * @var
	 */
	public $birthDate;
}
```


The `Aeris\ZendRestModule\View\Annotation\Groups` annotation allows you to configure which serialization groups will be used for each controller action.

```
use Aeris\ZendRestModule\View\Annotation as View;

class AnimalRestController extends AbstractRestfulController {
  
  /**
  * @View\Groups({"animalDetails", "dates"})
  */
  public function get($id) {
  	return $this->entityManager->find($id);
  }
  
  /**
  * @View\Groups({"animalSummary"})
  */
  public function getList() {
  	return $this->entityManager->findAll();
  }
}
```

You can also configure serialization groups in the `zend_rest` config:

```php
[
	'controllers' => [
    	'invokables' => [
        	'MyApp\Animals\AnimalRest' => '\MyApp\Animals\AnimalRestController',
        ]
    ],
	'zend_rest' => [
    	'controllers' => [
        	'MyApp\Animals\AnimalRest' => [
            	'serialization_groups' => [
                	'get' => ['animalDetails', 'dates'],
                    'getList' => ['animalSummary']
                ]
            ]
        ]
    ]
]
```


#### Other Serializer Components

##### DateTimeTimestampHandler

Serializes/deserializes between unix timestamps and `\DateTime` objects.

```php
class Animal {
	/**
     * @JMS\Type("DateTimeTimestamp")
     *
     * @var \DateTime
     */
	public $birthDate;
}
```
The serializer will now deserialize birthDate timestamps into \DateTime objects, and serialize birthDate as a timestamp.


#### Serializer Configuration Reference

```
[
	'zend_rest' => [
    	'serializer' => [
        	// Implementations of \JMS\Serializer\Handler\SubscribingHandlerInterface
            // See http://jmsyst.com/libs/serializer/master/handlers
        	'handlers' => [
            	// Registers the DateTimeTimestamp Handler by default,
                '\Aeris\ZendRestModule\Service\Serializer\Handler\DateTimeTimestampHandler',
            ],

            // An implementation of \JMS\Serializer\Naming\PropertyNamingStrategyInterface
            // The '\Aeris\ZendRestModule\Service\Serializer\Naming\IdenticalPropertyNamingStrategy` (default)
            // fixes a bug in the `\JMS\Serializer\Naming\IdenticalPropertyNamingStrategy`
            // See https://github.com/schmittjoh/serializer/issues/334
            'naming_strategy' => '\Aeris\ZendRestModule\Service\Serializer\Naming\IdenticalPropertyNamingStrategy',
            
            // An implementation of \JMS\Serializer\Construction\ObjectConstructorInterface
            // The 'Aeris\ZendRestModule\Serializer\Constructor\InitializedObjectConstructor' (default)
            // allows data to be deserialized onto existing entities.
            'object_constructor' => 'Aeris\ZendRestModule\Serializer\Constructor\InitializedObjectConstructor',
            
            // Set to false to disable the @MaxDepth annotation.
            // ZendRestModule sets this to true by default.
            // Note, however, that the JMSSerializers sets this to false by default.
            'enable_max_depth' => true,
        ]
	],
]
```




### RESTful Exception Handling

ZendRestModule catches errors and exceptions thrown during the MVC event cycle, and converts the errors into JSON responses.

#### Example

This example configures JSON output for errors occuring in the Animals Web Service.


```php
// zend-rest.config.php
'zend_rest' => [
	'errors' => [
    	[
        	// The `error` can be a Zend\Mvc error string
            // or the class name of an Exception
			'error' => \Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH,
            
            // HTTP response code
        	'http_code' => 404,
            
            The `error.code` property of the JSON response object
        	'application_code' => 'invalid_request',
            
            The `error.details` property of the JSON response object
        	'details' => 'The requested endpoint or action is invalid and not supported.',
        ],
        [
        	'error' => 'MyApp\Exception\RidiculousAnimalException',
            'http_code' => 418,
            'applicationCode' => 'ridiculous_animal_error',
            'details' => 'The animal you have requested is too riduculous for our web service.',
            'on_error' => function(RestErrorEvt $evt) {
            	// This is a good place to log errors
            	$exception = $evt->getError();
            	error_log($exception);
                
                // You can also modify the error view model
                $viewModel = $evt->getViewModel();
                $errorObject = $viewModel->getVariable('error');
                
                $errorObject['animal'] = $exception->getAnimal();
                $viewModel->setVariable('error', $errorObject);
            }
        ],
        [
            // You should always include a fallback '\Exception' handler.
        	'error' => '\Exception',
            'http_code' => 500,
            'application_code' => 'uh_oh',
            'details' => 'whoops!',
        ]
    ]
]
```

```php
class AnimalRestController extends AbstractRestfulController {
	public function get($id) {
    	if ($id === 'narwhal') {
        	throw new RidiculousAnimalException("narwhal");
        }
        
        return $this->animalRepo->find($id);
    }
}
```

 A `GET` request to `/animals/narwhal` would return a JSON object so:

```js
HTTP Code 418
{
	"error": {
    	"code": "ridiculous_animal_error",
        "details": "The animal you have requested is too riduculous for our web service.",
        "animal": "narwhal"
    }
}
```

The error would also be written to the server log, by way of the `on_error` callback.

Similarly a request to `/not/an/endpoint` would return a 404 error, with a `invalid_request` JSON application code.


### AbstractTestCase

The `\Aeris\ZendRestModuleTest\AbstractTestCase` is an extension of the `\Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase`. It provides a few utilities for testing restful APIs, and a number of new assertions. It is not necessary to use this test case class when working with the ZendRestModule.

### Configuration Reference


```php
[
	'zend_rest' => [
    	// Required.
    	'cache_dir' => __DIR__ . '/cache'
        
        // Set to true to disable caching.
        'debug' => false,
        

        // See "Exception Handling" documentation
        'errors' => [
          [
              'error' => \Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH,
              'http_code' => 404,
              'application_code' => 'invalid_request',
              'details' => 'The requested endpoint or action is invalid and not supported.',
              'on_error' => function(RestErrorEvt $evt) {
              	error_log("Someone requested an invalid endpoint.");
              }
          ]
        ],
        
        'controllers' => [
        	// See "Serialization Groups" documentation.
        	'serialization_groups' => [
            	'MyApp\Controller\UserRest' => [
                	'get' => ['details', 'timestamps'],
                    'getList' => ['summary']
                ]
            ] 
        ],
        
        // See "Serializer" documentation
        'serializer' => [
        	'handlers' [
                '\Aeris\ZendRestModule\Service\Serializer\Handler\DateTimeTimestampHandler',
            ],
            'naming_strategy' => '\Aeris\ZendRestModule\Service\Serializer\Naming\IdenticalPropertyNamingStrategy',
            'object_constructor' => 'Aeris\ZendRestModule\Serializer\Constructor\InitializedObjectConstructor',
            'enable_max_depth' => true,
        ]
    ]
]
```

Have fun!