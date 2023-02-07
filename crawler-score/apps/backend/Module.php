<?php

namespace Score\Backend;

use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\Url as UrlResolver;

class Module implements ModuleDefinitionInterface
{

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders(\Phalcon\DiInterface $di = NULL)
    {

        $loader = new Loader();

        $loader->registerNamespaces(array(
            'Score\Backend\Controllers' => __DIR__ . '/controllers/',
            'Score\Models' => __DIR__ . '/../models/',
            'Register\Models' => __DIR__ . '/../models/register/',
			'Score\Repositories' => __DIR__ . '/../repositories/',
			'Score\Utils' => __DIR__ . '/../library/Utils/',
			'Score\Google' => __DIR__ . '/../library/google-cloud-translate/'
        ));

        $loader->register();
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices(\Phalcon\DiInterface $di = NULL)
    {
		//Registering a dispatcher
		$di->set('dispatcher', function () use ($di) {
			$dispatcher = new \Phalcon\Mvc\Dispatcher();
			//Attach a event listener to the dispatcher
			$eventManager = new \Phalcon\Events\Manager();
			//$eventManager = $di->getShared('eventsManager');
			//$eventManager->attach('dispatch', new \Security('backend'));
			$dispatcher->setEventsManager($eventManager);
			$dispatcher->setDefaultNamespace("Score\Backend\Controllers");
			return $dispatcher;
		});

        /**
         * Setting up the view component
         */
        $di['view'] = function () {
			$view = new \Phalcon\Mvc\View();
			$view->setViewsDir(__DIR__ . '/views/');
			$view->registerEngines(array(
				'.volt' => function ($view, $di) {
		
					$volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
		
					$volt->setOptions(array(
						'compiledPath' => __DIR__ . '/cache/',
						'compiledSeparator' => '_'
					));
		
					return $volt;
				},
				'.phtml' => '\Phalcon\Mvc\View\Engine\Php'
			));
			$view->setLayoutsDir('layouts/');
			return $view;
        };
    }

}
