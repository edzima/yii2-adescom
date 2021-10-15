<?php

namespace Edzima\Yii2Adescom\tests\unit;

use Codeception\Test\Unit;
use Edzima\Yii2Adescom\models\SmsForm;
use UnitTester;
use yii\base\InvalidCallException;

class SmsFormTest extends Unit {

	/* @var UnitTester */
	protected $tester;

	private SmsForm $model;

	public function _before(): void {
		parent::_before();
		$this->model = new SmsForm();
	}

	public function testEmpty(): void {
		$this->tester->wantTo('Single Scenario');
		$this->model->scenario = SmsForm::SCENARIO_SINGLE;
		$this->tester->assertFalse($this->model->validate());
		$this->tester->seeModelError($this->model, 'phone', 'Phone number cannot be blank.');
		$this->tester->seeModelError($this->model, 'message', 'Message cannot be blank.');
		$this->tester->dontSeeModelError($this->model, 'phones');

		$this->tester->wantTo('Multiple Scenario');
		$this->model->scenario = SmsForm::SCENARIO_MULTIPLE;
		$this->tester->assertFalse($this->model->validate());
		$this->tester->seeModelError($this->model, 'phones', 'Phones numbers cannot be blank.');
		$this->tester->seeModelError($this->model, 'message', 'Message cannot be blank.');
		$this->tester->dontSeeModelError($this->model, 'phone');
	}

	/**
	 * @dataProvider normalizePhone
	 */
	public function testNormalizePhone(string $phone, string $normalized): void {
		$this->model->phone = $phone;
		$this->tester->assertTrue($this->model->validate(['phone']));
		$this->tester->assertSame($normalized, $this->model->phone);
	}

	/**
	 * @dataProvider normalizePhones
	 */
	public function testNormalizePhones(array $phones, array $normalized): void {
		$this->model->scenario = SmsForm::SCENARIO_MULTIPLE;
		$this->model->phones = $phones;
		$this->tester->assertTrue($this->model->validate(['phones']));
		foreach ($this->model->phones as $key => $phone) {
			$this->tester->assertSame($normalized[$key], $phone);
		}
	}

	public function testToShortNumber(): void {
		$this->model->phone = '123 123 123';
		$this->tester->assertFalse($this->model->validate());
		$this->tester->seeModelError($this->model, 'phone', 'Phone number should contain at least 11 characters.');

		$this->model->scenario = SmsForm::SCENARIO_MULTIPLE;
		$this->model->phones = ['123 123 123'];
		$this->tester->assertFalse($this->model->validate());
		$this->tester->seeModelError($this->model, 'phones', 'Phones numbers should contain at least 11 characters.');
	}

	public function testRemoveSpecialChars(): void {
		$this->model->message = 'ĄąĆćĘęŁłŃńÓóŚśŻżŹź';
		$this->model->removeSpecialCharacters = true;
		$this->assertTrue($this->model->validate(['message']));
		$this->tester->assertSame('AaCcEeLlNnOoSsZzZz', $this->model->message);

		$this->model->message = 'ĄąĆćĘęŁłŃńÓóŚśŻżŹź';
		$this->model->removeSpecialCharacters = false;
		$this->assertTrue($this->model->validate(['message']));
		$this->tester->assertSame('ĄąĆćĘęŁłŃńÓóŚśŻżŹź', $this->model->message);
	}

	public function testMessage(): void {
		$this->model->phone = '48 123 123 123';
		$this->model->message = 'Test Message';
		$this->model->validate();
		$message = $this->model->getMessage();
		$this->tester->assertSame('48123123123', $message->getDst());
		$this->tester->assertSame('Test Message', $message->getMessage());
		$messages = $this->model->getMessages();
		$message = reset($messages);
		$this->tester->assertSame('48123123123', $message->getDst());
		$this->tester->assertSame('Test Message', $message->getMessage());
	}

	public function testMessages(): void {
		$this->model->scenario = SmsForm::SCENARIO_MULTIPLE;
		$this->model->phones = [
			'48 123 123 123',
			'48 111 222 222',
		];
		$this->model->message = 'Test Message';
		$this->model->validate();
		$messages = $this->model->getMessages();
		$this->tester->assertCount(2, $messages);
		foreach ($messages as $message) {
			$this->tester->assertSame('Test Message', $message->getMessage());
		}
	}

	public function testSend(): void {
		$this->model->phone = '48123123123';
		$this->model->message = 'Test message';
		$this->tester->assertTrue($this->model->validate());
	}

	public function testWithoutOverwrite(): void {
		$this->model->sender->messageConfig['overwriteSrc'] = 'Test Over';
		$this->model->phone = '48123123123';
		$this->model->message = 'Test';
		$this->model->withOverwrite = true;
		$this->tester->assertSame('Test Over', $this->model->getMessage()->getOverwriteSrc());
		$this->model->withOverwrite = false;
		$this->tester->assertNull($this->model->getMessage()->getOverwriteSrc());
	}

	public function normalizePhone(): array {
		return [
			['+48 123 123 123', '48123123123'],
			['+48 123-123-123', '48123123123'],
			['0048 123-123-123', '48123123123'],
		];
	}

	public function normalizePhones(): array {
		return [
			[
				['+48 123 123 123'],
				['48123123123'],
			],
			[
				['+48 123 123 123', '+48 123-123-123', '0048 123-123-123'],
				['48123123123', '48123123123', '48123123123'],
			],
		];
	}

}
