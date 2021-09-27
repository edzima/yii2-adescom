<?php

use yii\base\Model;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class UnitTester extends \Codeception\Actor {

	use _generated\UnitTesterActions;

	/**
	 * Define custom actions here
	 */
	public function seeModelError(Model $model, string $attribute, string $message): void {
		$this->assertSame($message, $model->getFirstError($attribute));
	}
}
