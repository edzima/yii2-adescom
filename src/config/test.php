<?php

use yii\caching\ArrayCache;

$params = array_merge(
	require __DIR__ . '/params.php',
	require __DIR__ . '/params-local.php'
);

/**
 * Application configuration shared by all test types
 */
return [
	'id' => 'adescom-tests',
	'basePath' => dirname(__DIR__),
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
	],
	'language' => 'en-US',
	'components' => [
		'mailer' => [
			'useFileTransport' => true,
		],
		'assetManager' => [
			'basePath' => __DIR__ . '/../web/assets',
		],
		'urlManager' => [
			'showScriptName' => true,
		],
		'cache' => [
			'class' => ArrayCache::class,
		],
		'request' => [
			'cookieValidationKey' => 'test',
			'enableCsrfValidation' => false,
			// but if you absolutely need it set cookie domain to localhost
			/*
			'csrfCookie' => [
				'domain' => 'localhost',
			],
			*/
		],
	],
	'params' => $params,
];
