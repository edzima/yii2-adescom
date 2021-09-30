<?php

namespace Edzima\Yii2Adescom;

use Edzima\Yii2Adescom\models\MessageInterface;
use Edzima\Yii2Adescom\models\SmsSendRequest;
use yii\base\InvalidConfigException;
use yii\di\Instance;

class AdescomSender extends BaseSmsSender {

	public string $messageClass = SmsSendRequest::class;

	/**
	 * @var string|array|AdescomSoap
	 */
	public $client = [
		'class' => AdescomSoap::class,
	];

	/**
	 * @throws InvalidConfigException
	 */
	public function init() {
		parent::init();
		$this->client = Instance::ensure($this->client, AdescomSoap::class);
	}

	protected function sendMessage(MessageInterface $message): ?string {
		return $this->client->send($message);
	}
}
