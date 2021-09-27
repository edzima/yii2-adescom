<?php

namespace Edzima\Yii2Adescom\models;

use yii\base\Model;
use yii\di\Instance;

class SmsForm extends Model {

	public string $message = '';
	public string $phone = '';
	public bool $withOverwrite = true;

	/**
	 * @var string|array|SenderInterface
	 */
	public $sender = 'smsAdescom';

	public function init() {
		parent::init();
		$this->sender = Instance::ensure($this->sender, SenderInterface::class);
	}

	public function rules(): array {
		return [
			[['phone', 'message'], 'required'],
			['withOverwrite', 'boolean'],
			[['phone', 'message'], 'string'],
			['phone', 'filter', 'filter' => [$this, 'normalizePhone']],
			['phone', 'string', 'min' => 11, 'max' => 15],
		];
	}

	public static function normalizePhone(string $value): string {
		$value = preg_replace('/[^0-9.]+/', '', $value);
		return ltrim($value, '0');
	}

	public function send(): bool {
		if (!$this->validate()) {
			return false;
		}

		return !empty($this->sender->send($this->getMessage()));
	}

	public function getMessage(): MessageInterface {
		$message = $this->sender->compose();
		$message->setDst($this->phone);
		$message->setMessage($this->message);
		if (!$this->withOverwrite) {
			$message->setOverwriteSrc(null);
		}
		return $message;
	}

}
