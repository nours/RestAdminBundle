<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

// Composer autoload
if (!is_file($loaderFile = __DIR__.'/../../vendor/autoload.php')) {
    throw new \LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

$loader = require $loaderFile;

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// Loads NoursRestAdminBundle annotations
AnnotationRegistry::registerLoader(function($class) {
    $namespace = 'Nours\RestAdminBundle\Annotation';
    if (strpos($class, $namespace) === 0) {
        $className = explode('\\', $class);
        $className = array_pop($className);

        $file = __DIR__ . '/../../Annotation/' . $className . '.php';

        require $file;
        return true;
    }
});