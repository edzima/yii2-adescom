<?php

namespace Edzima\Yii2Adescom;

use Edzima\Yii2Adescom\exceptions\Exception;
use Edzima\Yii2Adescom\models\MessageInterface;
use SoapClient;
use SoapFault;
use stdClass;
use Yii;
use yii\base\Application;
use yii\base\Component;
use yii\base\Event;
use yii\helpers\VarDumper;

class AdescomSoap extends Component {

	public string $wsdlProto = 'https';
	public string $wsdlHost = 'platformy2.3s.pl';
	public string $wsdlHostPath = '/userpanel_webservices.php/mvnoservices/wsdl';

	public string $namespaceProto = 'https';
	public string $namespaceHost = 'platformy2.3s.pl';
	public string $namespaceHostPath = '/userpanel_webservices.php/mvnoservices/handler';

	public bool $disableWsdlCache = true;

	public array $options = [
		'soap_version' => SOAP_1_2,
		'trace' => 1,
		'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
	];

	public ?string $keySessionIdCache = 'adescom.sessionId';

	public int $loginDuration = 3600;

	private string $login;
	private string $password;

	private ?SoapClient $client = null;
	private ?string $sessionId = null;

	public function init() {
		parent::init();
		if ($this->disableWsdlCache) {
			ini_set("soap.wsdl_cache_enabled", "0");
		}
		$this->options['location'] = $this->getLocation();
		Event::on(Application::class, Application::EVENT_AFTER_REQUEST, function () {
			$this->logout();
		});
	}

	protected function setLogin(string $login): void {
		$this->login = $login;
	}

	protected function setPassword(string $password): void {
		$this->password = $password;
	}

	protected function getClient(): SoapClient {
		if ($this->client === null) {
			try {
				$options = $this->options;
				$options['location'] = $this->getLocation();
				$this->client = new SoapClient($this->getWsdlUrl(), $options);
			} catch (SoapFault $e) {
				throw new Exception($e->getMessage(), (int) $e->getCode(), $e);
			}
		}
		return $this->client;
	}

	public function send(MessageInterface $message): ?string {
		$options = new stdClass();
		$options->src = $message->getSrc();
		if ($message->getOverwriteSrc()) {
			$options->overwrite_src = $message->getOverwriteSrc();
		}
		$options->dst = $message->getDst();
		$options->max_retry_count = $message->getMaxRetryCount();
		$options->retry_interval = $message->getRetryInterval();
		$options->message = $message->getMessage();
		Yii::debug("Try send SMS for Message: " . VarDumper::export($message), 'adescomSoap.send');
		$response = $this->callMethod('smsSend', $options);
		Yii::debug("Success Send SMS. Response: " . VarDumper::export($response), 'adescomSoap.send');
		return $response->sms_id;
	}

	public function callMethod(string $method, ...$args) {
		if (!$this->isLogged()) {
			$this->auth();
		}
		try {
			return call_user_func([$this->getClient(), $method], ...$args);
		} catch (SoapFault $e) {
			if ($e->getMessage() === 'Authorization required!') {
				$this->auth();
				return call_user_func([$this->getClient(), $method], ...$args);
			}
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	public function auth(): string {
		$client = $this->getClient();
		if (!empty($this->keySessionIdCache) && $this->sessionId === null) {
			$this->sessionId = Yii::$app->cache->get($this->keySessionIdCache);
		}

		if (empty($this->sessionId)) {
			try {
				$this->sessionId = $client->login($this->login, $this->password, $this->loginDuration);
				Yii::debug("Adescom Success login with: login {$this->login}. SessionId: {$this->sessionId}", 'adescomSoap.auth');
				if (!empty($this->keySessionIdCache)) {
					Yii::$app->cache->set($this->keySessionIdCache, $this->sessionId, $this->loginDuration);
				}
				$client->__setCookie('sessionID', $this->sessionId);
			} catch (SoapFault $e) {
				$this->sessionId = null;
				throw new Exception($e->getMessage(), $e->getCode());
			}
		}
		return $this->sessionId;
	}

	public function logout(bool $deleteSessionCache = false): bool {
		if ($this->isLogged()) {
			if ($deleteSessionCache && !empty($this->keySessionIdCache)) {
				Yii::$app->cache->delete($this->keySessionIdCache);
			}
			try {
				return $this->getClient()->logout($this->sessionId);
			} catch (SoapFault $e) {
				throw new Exception($e->getMessage(), (int) $e->getCode(), $e);
			}
		}
		return false;
	}

	protected function isLogged(): bool {
		return !empty($this->sessionId);
	}

	private function getWsdlUrl(): string {
		return $this->wsdlProto . '://' . $this->wsdlHost . $this->wsdlHostPath;
	}

	private function getLocation(): string {
		return $this->namespaceProto . '://' . $this->namespaceHost . $this->namespaceHostPath;
	}
}
