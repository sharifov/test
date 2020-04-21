<?php

namespace modules\email\src\protocol\gmail;

use modules\email\src\entity\emailAccount\EmailAccount;
use Google_Client;
use Google_Service_Gmail;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/**
 * Class GmailClient
 *
 * @property Google_Client $client
 */
class GmailClient
{
    private $client;

    private function __construct()
    {
        $client = new Google_Client();
        $client->setScopes(Google_Service_Gmail::MAIL_GOOGLE_COM);

        $credentials = \Yii::$app->params['gmail_api_project_credentials'] ?? null;
        if ($credentials === null) {
            throw new \RuntimeException('Gmail API Project credentials not found.');
        } else {
            $credentials = Json::decode($credentials);
        }
        $client->setAuthConfig($credentials);

        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $this->client = $client;
    }

    public static function create(): Google_Client
    {
        return (new self())->getClient();
    }

    public static function createByAccount(EmailAccount $account): Google_Client
    {
        $client = (new self())->getClient();

        if ($account->ea_gmail_token) {
            $client->setAccessToken(Json::decode($account->ea_gmail_token));
        }

        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                throw new \RuntimeException('Cant get refresh access token for EmailAccount Id: "' . $account->ea_id . '" Email: "' . $account->ea_email . '". Need to request access token again.');
            }

            $account->ea_gmail_token = Json::encode($client->getAccessToken());
            if (!$account->save()) {
                throw new \RuntimeException(VarDumper::dumpAsString([
                    'account' => 'Id: "' . $account->ea_id . '" Email: "' . $account->ea_email . '"',
                    'message' => 'Cant save access token to DB',
                    'error' => $account->getErrors()
                ]));
            }
        }

        return $client;
    }

    public function getClient(): Google_Client
    {
        return $this->client;
    }
}
