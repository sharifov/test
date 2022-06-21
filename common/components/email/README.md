# Email Component
## Description
Email component for sending direct mails without communication, using `mailer` from box
## Installation
#### 1. Add to common/config/main.php
```php
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => true,
        ],
        'email' => [
            'class' => 'common\components\email\EmailComponent',
            'isActive' => true,
        ],
```

#### 2. Add to all env environments/{{ENV}}/common/config/main-local.php
```php
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => true,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => env('COMMON_CONFIG_MAIN_COMPONENTS_MAILER_HOST'),
                'port' => env('COMMON_CONFIG_MAIN_COMPONENTS_MAILER_PORT'),
                'username' => env('COMMON_CONFIG_MAIN_COMPONENTS_MAILER_USERNAME'),
                'password' => env('COMMON_CONFIG_MAIN_COMPONENTS_MAILER_PASSWORD'),
                'encryption' => env('COMMON_CONFIG_MAIN_COMPONENTS_MAILER_ENCRYPTION'),
            ],
        ],
```

#### 3. Copy from .env.example block to .env
```bash
# Mailer settings
COMMON_CONFIG_MAIN_COMPONENTS_MAILER_HOST=
COMMON_CONFIG_MAIN_COMPONENTS_MAILER_PORT=
COMMON_CONFIG_MAIN_COMPONENTS_MAILER_USERNAME=
COMMON_CONFIG_MAIN_COMPONENTS_MAILER_PASSWORD=
COMMON_CONFIG_MAIN_COMPONENTS_MAILER_ENCRYPTION=
```

## Using component
#### Example sending Email code verification:
```php
    Yii::$app
        ->email
        ->getService()
        ->sendEmailCodeVerification('2354234', Yii::$app->user->identity);
```