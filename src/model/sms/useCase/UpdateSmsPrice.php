<?php

namespace src\model\sms\useCase;

use src\repositories\sms\SmsRepository;
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

    public function update(array $smsSids): void
    {
        $result = [];

        try {
            $result = \Yii::$app->comms->getSmsPrice($smsSids);

            if ($result['error']) {
                throw new \DomainException($result['message']);
            }

// for test sms records for update price not finish
//            if (empty($result['result'])){
//                foreach ($smsSids as $sid) {
//                    $sms = $this->repo->findBySid($sid);
//                    $sms->s_updated_dt = date('Y-m-d H:i:s');
//                    $this->repo->save($sms);
//                }
//            }

            foreach ($result['result'] as $sid => $smsData) {
                if (!isset($smsData['price'])) {
//                \Yii::info(VarDumper::dumpAsString([
//                    'smsSid' => $smsSid,
//                    'message' => 'Not found price',
//                ]), 'info\UpdateSmsPrice');
                    continue;
                }

                $sms = $this->repo->findBySid($sid);
                $price = $smsData['price'];
                $validator = new NumberValidator();
                if (!$validator->validate($price, $error)) {
                    throw new \DomainException('Price Error: ' . $error);
                }
                $sms->setPrice(abs($price));
                $this->repo->save($sms);
            }
        } catch (\Throwable $e) {
            throw new \Exception(VarDumper::dumpAsString(['smsSid' => $sid, 'message' => $e->getMessage(), 'result' => $result]));
        }
    }
}
