<?php

namespace src\model\userAuthClient\service;

use yii\authclient\OAuth2;

class MicrosoftOAuthClient extends OAuth2
{
    public ?string $url;
    public ?string $tenantId;
    public ?string $authUri;
    public ?string $tokenUri;
    public $apiBaseUrl;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->authUrl = $this->url . $this->tenantId . $this->authUri;
        $this->tokenUrl = $this->url . $this->tenantId . $this->tokenUri;

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
