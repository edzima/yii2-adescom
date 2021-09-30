<?php

namespace Edzima\Yii2Adescom\tests\unit;

use Codeception\Test\Unit;
use Edzima\Yii2Adescom\AdescomSoap;
use Edzima\Yii2Adescom\models\SmsSendRequest;
use SoapFault;
use UnitTester;
use Yii;

class AdescomSoapTest extends Unit {

	/* @var UnitTester */
	protected $tester;

	private AdescomSoap $adescom;

	public function _before() {
		parent::_before();
	}

	public function _after() {
		parent::_after();
		$this->adescom->logout();
	}

	public function testInvalidLogin(): void {
		$this->tester->expectThrowable(SoapFault::class, function () {
			$this->giveAdescom(['login' => '', 'password' => '']);
			$this->adescom->auth();
		});
	}

	public function testAuth(): void {
		$this->giveAdescom();
		$sessionId = $this->adescom->auth();
		$this->tester->assertNotEmpty($sessionId);
		$this->tester->assertSame($sessionId, Yii::$app->cache->get($this->adescom->keySessionIdCache));
	}

	public function testLogoutWithoutAuth(): void {
		$this->giveAdescom();
		$this->tester->assertFalse($this->adescom->logout());
	}

	public function testSendSMS(): void {
		if (!isset(Yii::$app->params['adescom.test.sendSMS'])) {
			$this->addWarning('Not Found Number');
		} else {
			$this->giveAdescom();
			/** @var SmsSendRequest $message */
			$message = $this->adescom->compose(Yii::$app->params['adescom.test.sendSMS']);
			$this->adescom->auth();
			$smsId = $this->adescom->send($message);
			$this->tester->assertNotEmpty($smsId);
		}
	}

	private function giveAdescom(array $config = []) {
		if (!array_key_exists('login', $config)) {
			$config['login'] = Yii::$app->params['adescom.login'];
		}
		if (!array_key_exists('password', $config)) {
			$config['password'] = Yii::$app->params['adescom.password'];
		}
		$this->adescom = new AdescomSoap($config);
	}

}
