<?php

namespace Edzima\Yii2Adescom\models;

class SmsSendRequest extends BaseMessage {

	private string $message;
	private string $src;
	private string $dst;
	private int $max_retry_count;
	private int $retry_interval;
	private ?string $overwrite_src = null;
	private ?string $external_id = null;

	public function setMessage(string $message): MessageInterface {
		$this->message = $message;
		return $this;
	}

	public function getMessage(): string {
		return $this->message;
	}

	public function setSrc(string $src): MessageInterface {
		$this->src = $src;
		return $this;
	}

	public function getSrc(): string {
		return $this->src;
	}

	public function setOverwriteSrc(?string $overwrite): MessageInterface {
		$this->overwrite_src = $overwrite;
		return $this;
	}

	public function getOverwriteSrc(): ?string {
		return $this->overwrite_src;
	}

	public function setDst(string $dst): MessageInterface {
		$this->dst = $dst;
		return $this;
	}

	public function getDst(): string {
		return $this->dst;
	}

	public function setMaxRetryCount(int $max): MessageInterface {
		$this->max_retry_count = $max;
		return $this;
	}

	public function getMaxRetryCount(): int {
		return $this->max_retry_count;
	}

	public function setRetryInterval(int $value): MessageInterface {
		$this->retry_interval = $value;
		return $this;
	}

	public function getRetryInterval(): int {
		return $this->retry_interval;
	}

	public function setExternalId(?string $id): void {
		$this->external_id = $id;
	}

	public function getExternalId(): ?string {
		return $this->external_id;
	}

	public function toArray(): array {
		$array = parent::toArray();
		$array['external_id'] = $this->getExternalId();
		return $array;
	}
}
