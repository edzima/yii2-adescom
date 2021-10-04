<?php

namespace Edzima\Yii2Adescom\tests\unit;

use Codeception\Test\Unit;
use Edzima\Yii2Adescom\models\SmsForm;
use UnitTester;

class SmsFormTest extends Unit {

	/* @var UnitTester */
	protected $tester;

	private SmsForm $model;

	public function _before(): void {
		parent::_before();
		$this->model = new SmsForm();
	}

	public function testEmpty(): void {
		$this->tester->assertFalse($this->model->validate());
		$this->tester->seeModelError($this->model, 'phone', 'Phone number cannot be blank.');
		$this->tester->seeModelError($this->model, 'message', 'Message cannot be blank.');
	}

	/**
	 * @dataProvider normalizePhone
	 */
	public function testNormalizePhone(string $phone, string $normalized): void {
		$this->model->phone = $phone;
		$this->tester->assertTrue($this->model->validate(['phone']));
		$this->tester->assertSame($normalized, $this->model->phone);
	}

	public function testToShortNumber(): void {
		$this->model->phone = '123 123 123';
		$this->tester->assertFalse($this->model->validate());
		$this->tester->seeModelError($this->model, 'phone', 'Phone number should contain at least 11 characters.');
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

}
