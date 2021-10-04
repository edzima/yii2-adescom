<?php

use Edzima\Yii2Adescom\models\SmsForm;
use Edzima\Yii2Adescom\Module;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model SmsForm */

$this->title = Module::t('Send SMS Message');
$overwriteSrc = $model->getMessage()->getOverwriteSrc();
?>


<div class="sms-push-form">

	<?php $form = ActiveForm::begin([
		'id' => 'sms-push-form',
	]); ?>

	<?= $form->field($model, 'phone')->textInput() ?>

	<?= $form->field($model, 'message')->textarea() ?>

	<?= $form->field($model, 'removeSpecialCharacters')->checkbox() ?>

	<?= $overwriteSrc !== null
		? $form->field($model, 'withOverwrite')->checkbox()->hint($overwriteSrc)
		: ''
	?>

	<div class="form-group">
		<?= Html::submitButton(Module::t('Send'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

