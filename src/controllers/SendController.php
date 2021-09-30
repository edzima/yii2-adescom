<?php

namespace Edzima\Yii2Adescom\controllers;

use Edzima\Yii2Adescom\models\SmsForm;
use Edzima\Yii2Adescom\Module;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class SendController extends Controller {

	public function actionPush(string $returnUrl = null) {
		$model = new SmsForm();
		if ($model->load(Yii::$app->request->post()) && $model->send()) {
			$message = $model->getMessage();
			Yii::$app->session->addFlash(
				'success',
				Module::t('Send SMS Message: {message} to: {dst}.', [
					'dst' => $message->getDst(),
					'message' => $message->getMessage(),
				])
			);
			if ($returnUrl === null) {
				$returnUrl = Url::home();
			}
			return $this->redirect($returnUrl);
		}
		return $this->render('push', [
			'model' => $model,
		]);
	}
}
