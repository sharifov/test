<?php

namespace src\services\microsoft;

use yii\authclient\OAuth2;

class Microsoft extends OAuth2
{
    public string $host;
    public string $tenantId;
    public $authUrl;
    public $tokenUrl;
    public $apiBaseUrl;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->authUrl = $this->host . $this->tenantId . $this->authUrl;
        $this->tokenUrl = $this->host . $this->tenantId . $this->tokenUrl;

        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(',', [
                'wl.basic',
                'wl.emails',
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function initUserAttributes()
    {
        return $this->api('me', 'GET');
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultName()
    {
        return 'microsoft';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultTitle()
    {
        return 'Microsoft';
    }

    /**
     * {@inheritdoc}
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $request->getHeaders()->set('Authorization', 'Bearer ' . $accessToken->getToken());
    }
}
