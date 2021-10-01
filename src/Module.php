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
		if (!isset(Yii::$app->i18n->translations['edzima/adescom'])) {
			Yii::$app->i18n->translations['edzima/adescom'] = [
				'class' => 'yii\i18n\PhpMessageSource',
				'sourceLanguage' => 'en-US',
				'basePath' => __DIR__ . '/messages',
			];
		}
	}

	public static function t($message, $params = [], $language = null) {
		return Yii::t('edzima/adescom', $message, $params, $language);
	}

}
