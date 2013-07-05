<?php

namespace SclZfSingleEntityController;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

/**
 * Module for SclZfSingleEntityController library.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Module implements
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     *
     * Attach the listener to the event manager.
     *
     * @param EventInterface $e
     */
    public function onBootstrap(EventInterface $e)
    {
        $app            = $e->getApplication();
        $serviceManager = $app->getServiceManager();
        $eventManager   = $serviceManager->get('SharedEventManager');

        $listener = $serviceManager->get('SclZfSingleEntityController\Listener\ControllerListener');

        $eventManager->attachAggregate($listener);
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'SclZfSingleEntityController\Listener\ControllerListener'
                    => 'SclZfSingleEntityController\Listener\ControllerListener',
            ),
        );
    }
}
