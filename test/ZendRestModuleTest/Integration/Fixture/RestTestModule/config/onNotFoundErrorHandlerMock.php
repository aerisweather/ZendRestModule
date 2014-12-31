<?php

use \Mockery as M;

$onNotFoundErrorHandlerMock = M::mock('stdClass')
	->shouldReceive('onError')
	->withAnyArgs()
	->byDefault();


return $onNotFoundErrorHandlerMock;
