<?php

namespace Edzima\Yii2Adescom\events;

use Edzima\Yii2Adescom\models\MessageInterface;
use yii\base\Event;

/**
 * SMSEvent represents the event parameter used for events triggered by [[BaseSmsSender]].
 *
 * By setting the [[isValid]] property, one may control whether to continue running the action.
 *
 * @author Edzi Ma <lukasz.wojda@protonmail.com>
 */
class SMSEvent extends Event {

	/**
	 * Message being send.
	 */
	public MessageInterface $message;

	public ?string $id;

	/**
	 * @var bool whether to continue sending an sms. Event handlers of
	 * [[BaseSmsSender::EVENT_BEFORE_SEND]] may set this property to decide whether
	 * to continue send or not.
	 */
	public bool $isValid = true;
}
