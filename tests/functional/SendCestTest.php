<?php

use Codeception\Util\Maybe;
use Edzima\Yii2Adescom\AdescomSender;
use Edzima\Yii2Adescom\controllers\SendController;
use Edzima\Yii2Adescom\events\SMSEvent;
use Edzima\Yii2Adescom\models\MessageInterface;
use yii\base\Event;

class SendCestTest extends \Codeception\Test\Unit {

	private const SELECTOR_FORM = '#sms-push-form';
	protected FunctionalTester $tester;

	/** @see SendController::actionPush() */
	private const ROUTE_PUSH = '/sms/send/push';
	private MessageInterface $lastMessage;

	protected function _before(): void {
		Yii::$app->set('assetManager', new Maybe());
		Event::on(AdescomSender::class, AdescomSender::EVENT_AFTER_SEND, function (SMSEvent $e) {
			$this->lastMessage = $e->message;
		});
	}

	protected function _after() {
	}

	public function testSend(): void {
		$I = $this->tester;
		$I->amOnPage(static::ROUTE_PUSH);
		$I->see('Send SMS Message');
		$I->submitForm(static::SELECTOR_FORM, $this->formParams('48123123123', 'Test Message'));
		$this->tester->assertSame('Test Message', $this->lastMessage->getMessage());
		$this->tester->assertSame('48123123123', $this->lastMessage->getDst());
	}

	public function testSendEmpty(): void {
		$I = $this->tester;
		$I->amOnPage(static::ROUTE_PUSH);
		$I->see('Send SMS Message');
		$I->submitForm(static::SELECTOR_FORM, $this->formParams('', ''));
		$I->see('Phone cannot be blank.');
		$I->see('Message cannot be blank.');
	}

	private function formParams($phone, $message, $withOverwrite = true): array {
		return [
			"SmsForm[phone]" => $phone,
			"SmsForm[message]" => $message,
			"SmsForm[withOverwrite]" => $withOverwrite,
		];
	}
}
