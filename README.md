<p align="center">
    <h1 align="center">Yii 2 Adescom Module</h1>
    <br>
</p>

Component for Send SMS from Adescom.

### Install

Either run

```
$ php composer.phar require edzima/yii2-adescom
```

or add

```
"edzima/yii2-adescom": "^0.1"
```

to the ```require``` section of your `composer.json` file.

### Configuration

```php
'components' => [
    'sms' => [
        'class' => 'Edzima\Yii2Adescom\AdescomSoap',
        'login' => 'your_login',
        'password' => 'your_password',
        // overwrite default 
        'composeConfig' => [
            'class' => 'Edzima\Yii2Adescom\models\SmsSendRequest',
            'overwriteSrc' => 'EDZIMA'
        ],   
        'wsdlHost' => 'other.host.com',
        'keySessionIdCache' => null // disable cache sessionId
        'loginDuration' => 7200,
    ]
    // ...
]
```

### Usage

Compose from Array

```php
$message = Yii::$app->sms->compose([
	'message' => 'Test',
	'src' => 'Src',
	'dst' => 'Dst',
	'overwriteSrc' => 'Overwrite',
	'maxRetryCount' => 1,
	'retryInterval' => 60,
	]
);
```

OR Compose from Object

```php
$message = Yii->$app->sms->compose()
    ->setSrc('Source Number')
    ->setOverwriteSrc('Overwrite Text')
    ->setDst('Destination Number')
    ->setRetryInterval(60)
    ->setMaxRetryCount(1)
    ->setMessage('Message Text');
```

Send Message

```php
$smsId = Yii->$app->sms->send($message);
```
