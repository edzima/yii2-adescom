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
			'&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae',
			'&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
			'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
			'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
			'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
			'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
			'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
			'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'L', 'Ľ' => 'L',
			'Ĺ' => 'L', 'Ļ' => 'L', 'Ŀ' => 'L', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
			'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
			'Ö' => 'Oe', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
			'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
			'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
			'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
			'&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
			'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
			'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
			'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
			'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
			'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
			'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
			'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
			'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
			'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
			'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
			'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
			'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
			'&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
			'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ś' => 's', 'ù' => 'u', 'ú' => 'u',
			'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
			'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
			'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
			'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
			'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
			'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
			'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
			'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
			'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
			'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
			'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
			'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
			'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
			'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
			'ю' => 'yu', 'я' => 'ya',
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
