<?php

use Edzima\Yii2Adescom\models\SmsForm;
use Edzima\Yii2Adescom\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $model SmsForm */

$this->title = Module::t('Send SMS Message');
?>


<div class="sms-push-form">

	<?php $form = ActiveForm::begin([
		'id' => 'sms-push-form',
	]); ?>

	<?= $form->field($model, 'phone')->textInput() ?>

	<?= $form->field($model, 'message')->textarea() ?>

	<?= $form->field($model, 'withOverwrite')->checkbox()->hint($model->getMessage()->getOverwriteSrc()) ?>

	<div class="form-group">
		<?= Html::submitButton(Module::t('Send'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

