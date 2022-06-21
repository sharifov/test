<?php

namespace common\components\email;

use yii\base\Component;

class EmailComponent extends Component
{
    public bool $isActive = false;
    private ?EmailService $service = null;

    /**
     * @return EmailService|null
     */
    public function getService(): ?EmailService
    {
        return $this->service;
    }

    /**
     * @param EmailService|null $service
     */
    public function setService(?EmailService $service): void
    {
        $this->service = $service;
    }

    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();
        if (empty($this->service)) {
            $this->setService(new EmailService($this));
        }
    }

    /**
     * Getting email_from property from SiteSettings
     * @return string
     */
    public function getEmailFrom(): string
    {
        $email = \Yii::$app->params['settings']['email_component']['email_from'] ?? '';
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }
        return '';
    }
}
