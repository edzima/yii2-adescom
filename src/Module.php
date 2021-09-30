<?php

namespace Edzima\Yii2Adescom;

use Yii;
use yii\base\Module as BaseModule;

class Module extends BaseModule {

	public $controllerNamespace = 'Edzima\Yii2Adescom\controllers';

	public function init() {
		parent::init();
		$this->registerTranslations();
	}

	public function registerTranslations(): void {
		Yii::$app->i18n->translations['edzima/Yii2Adescom/*'] = [
			'class' => 'yii\i18n\PhpMessageSource',
			'sourceLanguage' => 'en-US',
			'basePath' => '@edzima/Yii2Adescom/messages',
			'fileMap' => [
				'edzima/Yii2Adescom/adescom' => 'adescom.php',
			],
		];
	}

	public static function t($message, $params = [], $language = null) {

		return Yii::t('edzima/Yii2Adescom/adescom', $message, $params, $language);
	}

}
