<?php

namespace Edzima\Yii2Adescom\tests\unit;

use Codeception\Test\Unit;
use Edzima\Yii2Adescom\AdescomSender;
use Edzima\Yii2Adescom\events\SMSEvent;
use Edzima\Yii2Adescom\models\MessageInterface;
use Edzima\Yii2Adescom\models\SmsSendRequest;
use UnitTester;
use Yii;

class AdescomSenderTest extends Unit {

	protected UnitTester $tester;

	private AdescomSender $sender;
	/** @var MessageInterface[] */
	private array $messages = [];

	public function testComposeMessageInstance(): void {
		$this->giveSender();
		$this->tester->assertInstanceOf(SmsSendRequest::class, $this->sender->compose());
	}

	public function testComposeFromArray(): void {
		$this->giveSender();
		$message = $this->sender->compose([
			'message' => 'Test',
			'src' => 'Src',
			'dst' => 'Dst',
			'overwriteSrc' => 'Overwrite',
			'maxRetryCount' => 1,
			'retryInterval' => 60,
		]);
		$this->tester->assertSame($message->getMessage(), 'Test');
		$this->tester->assertSame($message->getSrc(), 'Src');
		$this->tester->assertSame($message->getDst(), 'Dst');
		$this->tester->assertSame($message->getOverwriteSrc(), 'Overwrite');
		$this->tester->assertSame($message->getMaxRetryCount(), 1);
		$this->tester->assertSame($message->getRetryInterval(), 60);
	}

	public function testComposeMessageWithMessageConfig(): void {
		$this->giveSender();

		$this->sender->messageConfig['overwriteSrc'] = 'Overwrite Src From Config';

		$message = $this->sender->compose();
		$this->tester->assertSame('Overwrite Src From Config', $message->getOverwriteSrc());

		$message = $this->sender->compose([
			'overwriteSrc' => 'Test Overwrite',
		]);
		$this->tester->assertSame('Test Overwrite', $message->getOverwriteSrc());
	}

	public function testSendMessage(): void {
		$this->giveSender();
		$message = $this->sender->compose()
			->setMessage('Test Message')
			->setDst('123123123')
			->setSrc('222111333')
			->setMaxRetryCount(1)
			->setRetryInterval(60);
		$id = $this->sender->send($message);
		$this->tester->assertNotEmpty($id);
		$file = Yii::getAlias($this->sender->fileTransportPath . '/' . $id);
		$this->tester->assertFileExists($file);
		$this->tester->assertNotEmpty($this->messages);
		$message = reset($this->messages);
		$this->tester->assertSame('Test Message',$message->getMessage());
	}

	private function giveSender(array $config = []): void {
		$this->sender = new AdescomSender($config);
		$this->sender->useFileTransport = true;
		$this->sender->on(AdescomSender::EVENT_AFTER_SEND, function (SMSEvent $event) {
			$this->messages[] = $event->message;
		});
	}

}
