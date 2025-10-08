<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('app_events', '/events')
        ->controller([App\Controller\EventController::class, 'list'])
        ->methods(['GET']);
};




?>
