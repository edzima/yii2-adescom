<?php

namespace Edzima\Yii2Adescom\models;

interface SenderInterface {

	public function compose(array $config = []): MessageInterface;

	/**
	 * @param MessageInterface $message
	 * @return string Message ID
	 */
	public function send(MessageInterface $message): ?string;

}
