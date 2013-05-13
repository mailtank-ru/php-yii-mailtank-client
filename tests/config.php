<?php

return array(
    'basePath' => __DIR__ . '/..',

    'preload' => array('mailtank'),

    // application components
    'components' => array(
        'mailtank' => array(
            'class' => 'application.MailtankClient',
            'host' => $params['host'],
            'token' => $params['token'],
        ),
    ),
);