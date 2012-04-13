<?php

Autoloader::add_core_namespace('Pop3');

Autoloader::add_classes(array(
    /**
     * Email classes.
     */
    'Pop3\\Pop3' => __DIR__ . '/classes/pop3.php',
    'Pop3\\NoConnectionException'	=> __DIR__.'/classes/pop3.php',
));
