<?php

namespace Edzima\Yii2Adescom\models;

interface SenderInterface {

	public function compose(array $params = []): MessageInterface;

	/**
	 * @param MessageInterface $message
	 * @return string Message ID on Successful send or null when don't.
	 */
	public function send(MessageInterface $message): ?string;

}
