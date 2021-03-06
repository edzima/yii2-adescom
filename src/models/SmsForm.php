<?php

namespace Edzima\Yii2Adescom\models;

use Edzima\Yii2Adescom\Module;
use yii\base\InvalidCallException;
use yii\base\Model;
use yii\di\Instance;
use yii\base\InvalidConfigException;

class SmsForm extends Model {

	public const SCENARIO_DEFAULT = self::SCENARIO_SINGLE;
	public const SCENARIO_SINGLE = 'single';
	public const SCENARIO_MULTIPLE = 'multiple';

	public array $phones = [];
	public string $message = '';
	public string $phone = '';
	public bool $withOverwrite = true;
	public bool $removeSpecialCharacters = true;

	/**
	 * @var string|array|SenderInterface
	 */
	public $sender = 'sms';

	/**
	 * {@inheritDoc}
	 * @throws InvalidConfigException when Sender is not ensured.
	 */
	public function init(): void {
		parent::init();
		Module::registerTranslations();
		$this->sender = Instance::ensure($this->sender, SenderInterface::class);
		if ($this->scenario === Model::SCENARIO_DEFAULT) {
			$this->scenario = static::SCENARIO_DEFAULT;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function rules(): array {
		return [
			[['message', 'withOverwrite', 'removeSpecialCharacters'], 'required'],
			['phone', 'required', 'on' => static::SCENARIO_SINGLE],
			['phones', 'required', 'on' => static::SCENARIO_MULTIPLE],
			[['withOverwrite', 'removeSpecialCharacters'], 'boolean'],
			['phone', 'filter', 'filter' => [$this, 'normalizePhone'], 'on' => static::SCENARIO_SINGLE],
			['phone', 'string', 'min' => 11, 'max' => 15, 'on' => static::SCENARIO_SINGLE],
			['phones', 'filter', 'filter' => [$this, 'normalizePhones'], 'on' => static::SCENARIO_MULTIPLE],
			['phones', 'each', 'rule' => ['string', 'min' => 11, 'max' => 15], 'on' => static::SCENARIO_MULTIPLE],

			[['message'], 'string', 'min' => 3],
			['message', 'filter', 'filter' => [$this, 'normalizeMessage']],
		];
	}

	public function attributeLabels(): array {
		return [
			'phone' => Module::t('Phone number'),
			'phones' => Module::t('Phones numbers'),
			'message' => Module::t('Message'),
			'withOverwrite' => Module::t('With Overwrite'),
			'removeSpecialCharacters' => Module::t('Remove special characters'),
		];
	}

	public function normalizePhones(array $phones): array {
		$normalize = [];
		foreach ($phones as $key => $phone) {
			$normalize[$key] = static::normalizePhone($phone);
		}
		return $normalize;
	}

	public static function normalizePhone(string $value): string {
		$value = preg_replace('/[^0-9.]+/', '', $value);
		return ltrim($value, '0');
	}

	public function normalizeMessage(string $value): string {
		if ($this->removeSpecialCharacters) {
			$replace = static::replaceChars();
			$value = str_replace(array_keys($replace), $replace, $value);
		}
		return $value;
	}

	protected static function replaceChars(): array {
		return [
			'&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
			'&quot;' => '', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'Ae',
			'&Auml;' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'Ae',
			'??' => 'C', '??' => 'C', '??' => 'C', '??' => 'C', '??' => 'C', '??' => 'D', '??' => 'D',
			'??' => 'D', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E',
			'??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'G', '??' => 'G',
			'??' => 'G', '??' => 'G', '??' => 'H', '??' => 'H', '??' => 'I', '??' => 'I',
			'??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I',
			'??' => 'I', '??' => 'IJ', '??' => 'J', '??' => 'K', '??' => 'L', '??' => 'L',
			'??' => 'L', '??' => 'L', '??' => 'L', '??' => 'N', '??' => 'N', '??' => 'N',
			'??' => 'N', '??' => 'N', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O',
			'??' => 'Oe', '&Ouml;' => 'Oe', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O',
			'??' => 'OE', '??' => 'R', '??' => 'R', '??' => 'R', '??' => 'S', '??' => 'S',
			'??' => 'S', '??' => 'S', '??' => 'S', '??' => 'T', '??' => 'T', '??' => 'T',
			'??' => 'T', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'Ue', '??' => 'U',
			'&Uuml;' => 'Ue', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U',
			'??' => 'W', '??' => 'Y', '??' => 'Y', '??' => 'Y', '??' => 'Z', '??' => 'Z',
			'??' => 'Z', '??' => 'T', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a',
			'??' => 'ae', '&auml;' => 'ae', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a',
			'??' => 'ae', '??' => 'c', '??' => 'c', '??' => 'c', '??' => 'c', '??' => 'c',
			'??' => 'd', '??' => 'd', '??' => 'd', '??' => 'e', '??' => 'e', '??' => 'e',
			'??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e',
			'??' => 'f', '??' => 'g', '??' => 'g', '??' => 'g', '??' => 'g', '??' => 'h',
			'??' => 'h', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'i',
			'??' => 'i', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'ij', '??' => 'j',
			'??' => 'k', '??' => 'k', '??' => 'l', '??' => 'l', '??' => 'l', '??' => 'l',
			'??' => 'l', '??' => 'n', '??' => 'n', '??' => 'n', '??' => 'n', '??' => 'n',
			'??' => 'n', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'oe',
			'&ouml;' => 'oe', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'oe',
			'??' => 'r', '??' => 'r', '??' => 'r', '??' => 's', '??' => 's', '??' => 'u', '??' => 'u',
			'??' => 'u', '??' => 'ue', '??' => 'u', '&uuml;' => 'ue', '??' => 'u', '??' => 'u',
			'??' => 'u', '??' => 'u', '??' => 'u', '??' => 'w', '??' => 'y', '??' => 'y',
			'??' => 'y', '??' => 'z', '??' => 'z', '??' => 'z', '??' => 't', '??' => 'ss',
			'??' => 'ss', '????' => 'iy', '??' => 'A', '??' => 'B', '??' => 'V', '??' => 'G',
			'??' => 'D', '??' => 'E', '??' => 'YO', '??' => 'ZH', '??' => 'Z', '??' => 'I',
			'??' => 'Y', '??' => 'K', '??' => 'L', '??' => 'M', '??' => 'N', '??' => 'O',
			'??' => 'P', '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'U', '??' => 'F',
			'??' => 'H', '??' => 'C', '??' => 'CH', '??' => 'SH', '??' => 'SCH', '??' => '',
			'??' => 'Y', '??' => '', '??' => 'E', '??' => 'YU', '??' => 'YA', '??' => 'a',
			'??' => 'b', '??' => 'v', '??' => 'g', '??' => 'd', '??' => 'e', '??' => 'yo',
			'??' => 'zh', '??' => 'z', '??' => 'i', '??' => 'y', '??' => 'k', '??' => 'l',
			'??' => 'm', '??' => 'n', '??' => 'o', '??' => 'p', '??' => 'r', '??' => 's',
			'??' => 't', '??' => 'u', '??' => 'f', '??' => 'h', '??' => 'c', '??' => 'ch',
			'??' => 'sh', '??' => 'sch', '??' => '', '??' => 'y', '??' => '', '??' => 'e',
			'??' => 'yu', '??' => 'ya',
		];
	}

	public function send(): bool {
		if (!$this->validate()) {
			return false;
		}

		if (!$this->isMultiple()) {
			return !empty($this->sender->send($this->getMessage()));
		}
		$send = true;
		foreach ($this->getMessages() as $message) {
			$send &= $this->sender->send($message);
		}
		return $send;
	}

	public function getMessage(string $phone = null): MessageInterface {
		$message = $this->sender->compose();
		$message->setDst($phone ?: $this->phone);
		$message->setMessage($this->message);
		if (!$this->withOverwrite) {
			$message->setOverwriteSrc(null);
		}
		return $message;
	}

	/**
	 * @return MessageInterface[]
	 */
	public function getMessages(): array {
		$messages = [];
		if ($this->isMultiple()) {
			foreach ($this->phones as $phone) {
				$messages[] = $this->getMessage($phone);
			}
		} else {
			$messages[] = $this->getMessage();
		}
		return $messages;
	}

	public function isMultiple(): bool {
		return $this->scenario === static::SCENARIO_MULTIPLE;
	}

}
