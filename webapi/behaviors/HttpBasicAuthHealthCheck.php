<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace webapi\behaviors;

use yii\filters\auth\AuthMethod;

/**
 * HttpBasicAuthHealthCheck is an action filter that supports the HTTP Basic authentication method for health-check action.
 */
class HttpBasicAuthHealthCheck extends AuthMethod
{
    /**
     * @var string the HTTP authentication realm
     */
    public $realm = 'api';

    public $auth;


    /**
     * {@inheritdoc}
     */
    public function authenticate($user, $request, $response)
    {
        if (empty(\Yii::$app->params['apiHealthCheck']) || empty(\Yii::$app->params['apiHealthCheck']['user'])) {
            return true;
        }
        list($username, $password) = $request->getAuthCredentials();
        if ((\Yii::$app->params['apiHealthCheck']['user'] == $username) && (\Yii::$app->params['apiHealthCheck']['password'] == $password)) {
            return true;
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function challenge($response)
    {
        $response->getHeaders()->set('WWW-Authenticate', "Basic realm=\"{$this->realm}\"");
    }
}
