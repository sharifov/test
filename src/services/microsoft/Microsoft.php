<?php

namespace src\services\microsoft;

use yii\authclient\OAuth2;

class Microsoft extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    public $authUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
    /**
     * {@inheritdoc}
     */
    public $tokenUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    /**
     * {@inheritdoc}
     */
    public $apiBaseUrl = 'https://graph.microsoft.com/v1.0';


    /**
     * {@inheritdoc}
     */
    public function init()
    {
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
