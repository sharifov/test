<?php

namespace sales\model\call\useCase;

use sales\repositories\call\CallRepository;
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

    public function update(string $callSid): void
    {
        $result = [];
        try {
            $call = $this->repo->findBySid($callSid);
            $result = \Yii::$app->communication->getCallPrice($call->c_call_sid);
            if ($result['error']) {
                throw new \DomainException($result['message']);
            }

            if (isset($result['result']['status']) && $result['result']['status'] === 'busy') {
                $call->setPrice(0);
                $this->repo->save($call);
                return;
            }

            if (!isset($result['result']['price'])) {
                throw new \DomainException('Not found price');
            }

            $price = $result['result']['price'];
            $validator = new NumberValidator();
            if (!$validator->validate($price, $error)) {
                throw new \DomainException('Price Error: ' . $error);
            }

            $call->setPrice(abs($price));
            $this->repo->save($call);
        } catch (\Throwable $e) {
            throw new \Exception(VarDumper::dumpAsString(['callSid' => $callSid, 'message' => $e->getMessage(), 'result' => $result]));
        }
    }
}
