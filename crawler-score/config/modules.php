<?php

/**
 * Register application modules
 */
$application->registerModules(array(
    'backend' => array(
        'className' => 'Score\Backend\Module',
        'path' => __DIR__ . '/../apps/backend/Module.php'
    ),
    'api' => array(
        'className' => 'Score\Api\Module',
        'path' => __DIR__ . '/../apps/api/Module.php'
    )
));
