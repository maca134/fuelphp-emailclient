<?php

Autoloader::add_core_namespace('Emailclient');

Autoloader::add_classes(array(
    /**
     * Emailclient classes.
     */
    'Emailclient\\Emailclient' => __DIR__ . '/classes/emailclient.php',
    'Emailclient\\NoConnectionException'	=> __DIR__.'/classes/emailclient.php',
));
