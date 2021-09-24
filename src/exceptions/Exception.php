<?php

namespace Edzima\Yii2Adescom\exceptions;

use yii\base\Exception as BaseException;

class Exception extends BaseException {

	public function getName() {
		return 'Adescom Exception';
	}
}
