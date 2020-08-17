<?php

namespace sales\model\sms\useCase;

use sales\repositories\sms\SmsRepository;
use yii\helpers\VarDumper;
use yii\validators\NumberValidator;

/**
 * Class UpdateSmsPrice
 *
 * @property SmsRepository $repo
 */
class UpdateSmsPrice
{
    private SmsRepository $repo;

    public function __construct(SmsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function update(string $smsSid): void
    {
        $result = [];
        try {
            $sms = $this->repo->findBySid($smsSid);
            $result = \Yii::$app->communication->getSmsPrice($sms->s_tw_message_sid);
            if ($result['error']) {
                throw new \DomainException($result['message']);
            }
            if (!isset($result['result']['price'])) {
                \Yii::info(VarDumper::dumpAsString([
                    'smsSid' => $smsSid,
                    'message' => 'Not found price',
                ]), 'info\UpdateSmsPrice');
                return;
            }
            $price = $result['result']['price'];
            $validator = new NumberValidator();
            if (!$validator->validate($price, $error)) {
                throw new \DomainException('Price Error: ' . $error);
            }
            $sms->setPrice(abs($price));
            $this->repo->save($sms);
        } catch (\Throwable $e) {
            throw new \Exception(VarDumper::dumpAsString(['smsSid' => $smsSid, 'message' => $e->getMessage(), 'result' => $result]));
        }
    }
}
