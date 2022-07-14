<?php

namespace src\model\call\useCase;

use src\repositories\call\CallRepository;
use yii\helpers\VarDumper;
use yii\validators\NumberValidator;

/**
 * Class UpdateCallPrice
 *
 * @property CallRepository $repo
 */
class UpdateCallPrice
{
    private CallRepository $repo;

    public function __construct(CallRepository $repo)
    {
        $this->repo = $repo;
    }

    public function update(array $callSids): void
    {
        $result = [];
        try {
            $result = \Yii::$app->comms->getCallPrice($callSids);

            if ($result['error']) {
                throw new \DomainException($result['message']);
            }

            foreach ($result['result'] as $sid => $callData) {
                $call = $this->repo->findBySid($sid);

                if (isset($callData['status']) && $callData['status'] === 'busy') {
                    $call->setPrice(0);
                    $this->repo->save($call);
                    continue;
                }

                if (!isset($callData['price'])) {
    //                \Yii::info(VarDumper::dumpAsString([
    //                    'callSid' => $callSid,
    //                    'message' => 'Not found price',
    //                ]), 'info\UpdateCallPrice');
                    continue;
                }

                $price = $callData['price'];
                $validator = new NumberValidator();
                if (!$validator->validate($price, $error)) {
                    throw new \DomainException('Price Error: ' . $error);
                }

                $call->setPrice(abs($price));
                $this->repo->save($call);
            }
        } catch (\Throwable $e) {
            throw new \Exception(VarDumper::dumpAsString(['callSid' => $sid, 'message' => $e->getMessage(), 'result' => $result]));
        }
    }
}
