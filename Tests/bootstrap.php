<?php

// Autoload
require $_SERVER['KERNEL_DIR'] . 'autoload.php';

// Clear cache
passthru(sprintf(
    'php "%sconsole" cache:clear --env=test --no-warmup',
    $_SERVER['KERNEL_DIR']
));

// Update sqlite DB
passthru(sprintf(
    'php "%s/console" doctrine:schema:update --env=test --force',
    $_SERVER['KERNEL_DIR']
));