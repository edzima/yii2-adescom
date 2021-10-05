<?php

namespace Edzima\Yii2Adescom;

use Edzima\Yii2Adescom\events\SMSEvent;
use Edzima\Yii2Adescom\models\BaseMessage;
use Edzima\Yii2Adescom\models\MessageInterface;
use Edzima\Yii2Adescom\models\SenderInterface;
use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\base\InvalidConfigException;

abstract class BaseSmsSender extends Component implements SenderInterface {

	/**
	 * @event SMSEvent an event raised right before send.
	 * You may set [[SMSEvent::isValid]] to be false to cancel the send.
	 */
	public const EVENT_BEFORE_SEND = 'beforeSend';
	/**
	 * @event SMSEvent an event raised right after send.
	 */
	public const EVENT_AFTER_SEND = 'afterSend';

	/**
	 * @var array the configuration that should be applied to any newly created
	 * email message instance by [[compose()]]. Any valid property defined
	 * by [[MessageInterface]] can be configured, such as `src`, `dst`, `overwriteSrc`, `message`, etc.
	 *
	 * For example:
	 *
	 * ```php
	 * [
	 *     'dst' => '48333222111',
	 *     'src' => '48111222333',
	 *     'overwriteSrc' => 'OVERWRITE',
	 * ]
	 * ```
	 */
	public array $messageConfig = [];
	/**
	 * @var string the default class name of the new message instances created by [[compose()]]
	 */
	public string $messageClass = BaseMessage::class;
	/**
	 * @var bool whether to save email messages as files under [[fileTransportPath]] instead of sending them
	 * to the actual recipients. This is usually used during development for debugging purpose.
	 * @see fileTransportPath
	 */
	public bool $useFileTransport = false;
	/**
	 * @var string the directory where the sms messages are saved when [[useFileTransport]] is true.
	 */
	public string $fileTransportPath = '@runtime/sms';

	/**
	 * {@inheritDoc}
	 */
	public function init() {
		if (YII_DEBUG && !$this->useFileTransport) {
			Yii::warning('In Debug mode recommended use file transport in SMS sender.', __METHOD__);
		}
		parent::init();
	}

	/**
	 * @param array $params
	 * @return MessageInterface
	 * @throws InvalidConfigException
	 */
	public function compose(array $params = []): MessageInterface {
		$config = $this->messageConfig;
		if (!array_key_exists('class', $config)) {
			$config['class'] = $this->messageClass;
		}
		$config = array_merge($config, $params);
		$config['sender'] = $this;
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($config);
	}

	/**
	 * Sends the given SMS message.
	 * This method will log a message about the sms being sent.
	 * If [[useFileTransport]] is true, it will save the sms as a file under [[fileTransportPath]].
	 * Otherwise, it will call [[sendMessage()]] to send the sms to its recipient(s).
	 * Child classes should implement [[sendMessage()]] with the actual sms sending logic.
	 *
	 * @param MessageInterface $message SMS message instance to be sent
	 * @return string|null SMS ID when successful send or NULL when don't.
	 */
	public function send(MessageInterface $message): ?string {
		if (!$this->beforeSend($message)) {
			return null;
		}

		Yii::info('Sending SMS "' . $message->getMessage() . '" to "' . $message->getDst() . '"', __METHOD__);

		if ($this->useFileTransport) {
			$id = $this->saveMessage($message);
		} else {
			$id = $this->sendMessage($message);
		}
		$this->afterSend($message, $id);

		return $id;
	}

	/**
	 * This method is invoked right before SMS send.
	 * You may override this method to do last-minute preparation for the message.
	 * If you override this method, please make sure you call the parent implementation first.
	 *
	 * @param MessageInterface $message
	 * @return bool whether to continue sending an sms.
	 */
	public function beforeSend(MessageInterface $message): bool {
		$event = new SMSEvent(['message' => $message]);
		$this->trigger(self::EVENT_BEFORE_SEND, $event);

		return $event->isValid;
	}

	/**
	 * This method is invoked right after SMS was send.
	 * You may override this method to do some postprocessing or logging based on SMS send status.
	 * If you override this method, please make sure you call the parent implementation first.
	 *
	 * @param MessageInterface $message
	 * @param string|null $id
	 */
	public function afterSend(MessageInterface $message, ?string $id): void {
		$event = new SMSEvent(['message' => $message, 'id' => $id]);
		$this->trigger(self::EVENT_AFTER_SEND, $event);
	}

	/**
	 * Sends the specified message.
	 * This method should be implemented by child classes with the actual SMS sending logic.
	 *
	 * @param MessageInterface $message the message to be sent
	 * @return string|null SMS ID when is sent successfully.
	 */
	abstract protected function sendMessage(MessageInterface $message): ?string;

	/**
	 * Saves the message as a file under [[fileTransportPath]].
	 *
	 * @param MessageInterface $message
	 * @return string generated message file name as ID.
	 */
	protected function saveMessage(MessageInterface $message): string {
		$path = Yii::getAlias($this->fileTransportPath);
		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}

		$name = $this->generateMessageFileName();

		$file = $path . '/' . $name;
		file_put_contents($file, VarDumper::export($message->toArray()));
		return $name;
	}

	/**
	 * @return string the file name for saving the message when [[useFileTransport]] is true.
	 */
	public function generateMessageFileName(): string {
		$time = microtime(true);

		return date('Ymd-His-', $time) . sprintf('%04d', (int) (($time - (int) $time) * 10000)) . '-' . sprintf('%04d', random_int(0, 10000)) . '.sms';
	}

}
