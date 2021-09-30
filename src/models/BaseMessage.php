<?php

namespace Edzima\Yii2Adescom\models;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;

abstract class BaseMessage extends BaseObject implements MessageInterface {

	public ?SenderInterface $sender = null;

	/**
	 * @throws InvalidConfigException
	 */
	public function send(SenderInterface $sender = null): ?string {
		if ($sender === null && $this->sender === null) {
			$sender = Yii::$app->get('sms');
		} elseif ($sender === null) {
			$sender = $this->sender;
		}

		return $sender->send($this);
	}

	public function toArray(): array {
		return [
			'message' => $this->getMessage(),
			'src' => $this->getSrc(),
			'dst' => $this->getDst(),
			'max_retry_count' => $this->getMaxRetryCount(),
			'retry_interval' => $this->getRetryInterval(),
			'overwrite_src' => $this->getOverwriteSrc(),
		];
	}
}
