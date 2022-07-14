<?php

namespace webapi\bootstrap;

use webapi\src\request\RequestBo;
use yii\base\BootstrapInterface;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = \Yii::$container;

        $container->setSingleton(RequestBo::class, static function () use ($app) {
            $client = new Client();
            $client->setTransport(CurlTransport::class);
            $request = $client
                ->createRequest()
                //->addHeaders(['Authorization' => 'Basic ' . base64_encode('username' . ':' . 'password')])
                ->setFormat(Client::FORMAT_JSON)
                ->setMethod('POST')
//                ->addData(['apiKey' => $app->params['bo']['apiKey']])
                ->setOptions([
                    CURLOPT_ENCODING => 'gzip',
                    'timeout' => 28,
                ]);
            return new RequestBo($request, $app->params['backOffice']['urlV2']);
        });
    }
}
