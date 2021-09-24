<?php

namespace Edzima\Yii2Adescom\models;

interface MessageInterface {

	public function setMessage(string $message): self;

	public function getMessage(): string;

	public function setSrc(string $src): self;

	public function getSrc(): string;

	public function setOverwriteSrc(?string $overwrite): self;

	public function getOverwriteSrc(): ?string;

	public function setDst(string $dst): self;

	public function getDst(): string;

	public function setMaxRetryCount(int $max): self;

	public function getMaxRetryCount(): int;

	public function setRetryInterval(int $value): self;

	public function getRetryInterval(): int;
}
