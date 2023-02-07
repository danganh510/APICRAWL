<?php

namespace Score\Api;

use Phalcon\DiInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher as Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Mvc\ModuleDefinitionInterface;


class Module implements ModuleDefinitionInterface
{

    /**
     * Registers an autoloader related to the module
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            'Score\Api\Controllers' => __DIR__ . '/controllers'
        ));
        $loader->register();
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        /**
         * Read configuration
         */
        //$config = $di['config'];

        //Registering a dispatcher
        $di->set('dispatcher', function () use ($di) {
            $eventsManager = new EventsManager();

            $dispatcher = new Dispatcher();
            $dispatcher->setEventsManager($eventsManager);
            $dispatcher->setDefaultNamespace('Score\Api\Controllers');
            return $dispatcher;
        });

        /**
         * Setting up the view component
         */
        $di['view'] = function () {
            $view = new View();

            if (defined('ENABLE_VIEW_MODE') && ENABLE_VIEW_MODE) {
                $view->setViewsDir(__DIR__ . '/views/');
                $view->registerEngines(array(
                    '.phtml' => '\Phalcon\Mvc\View\Engine\Php'
                ));
                $view->setLayoutsDir('layouts/');
            }
            else {
                $view->disable();
            }
            return $view;
        };

    }
}
