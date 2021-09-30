<?php

use Edzima\Yii2Adescom\AdescomSender;
use Edzima\Yii2Adescom\AdescomSoap;
use Edzima\Yii2Adescom\Module;
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
	'modules' => [
		'sms' => [
			'class' => Module::class,
		],
	],
	'components' => [
		'sms' => [
			'class' => AdescomSender::class,
			'messageConfig' => [
				'src' => '123123123',
				'maxRetryCount' => 1,
				'retryInterval' => 60,
			],
			'useFileTransport' => true,
			'client' => [
				'class' => AdescomSoap::class,
				'login' => $params['adescom.login'],
				'password' => $params['adescom.password'],
			],
		],
		'mailer' => [
			'useFileTransport' => true,
		],
		'assetManager' => [
			'basePath' => __DIR__ . '/../web/assets',
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
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
