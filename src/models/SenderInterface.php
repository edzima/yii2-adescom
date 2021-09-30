<?php

namespace Edzima\Yii2Adescom\models;

interface SenderInterface {

	public function compose(array $params = []): MessageInterface;

	/**
	 * @param MessageInterface $message
	 * @return string Message ID
	 */
	public function send(MessageInterface $message): ?string;

}
