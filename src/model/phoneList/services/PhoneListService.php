<?php

namespace src\model\phoneList\services;

use src\model\phoneList\entity\PhoneList;
use Yii;
use yii\helpers\VarDumper;
use yii\httpclient\Exception;

/**
 * Class PhoneListService
 * @package src\model\phoneList\services
 */
class PhoneListService
{
    /**
     * @return array
     * @throws Exception
     */
    public static function synchronizationPhoneNumbers(): array
    {
        $data = [
            'created' => [],
            'updated' => [],
            'error' => false
        ];

        $phoneNumbersData = Yii::$app->comms->phoneNumberList();
        if ($phoneNumbersData) {
            if (!empty($phoneNumbersData['error'])) {
                $data['error'] = 'Error: ' . $phoneNumbersData['error'];
            } elseif (!empty($phoneNumbersData['data']['phone-numbers'])) {
                foreach ($phoneNumbersData['data']['phone-numbers'] as $item) {
                    $exist = PhoneList::find()->where(['pl_phone_number' => $item['pn_phone_number']])->exists();

                    if ($exist) {
                        continue;
                    }

                    $phone = new PhoneList();
                    $phone->pl_phone_number = $item['pn_phone_number'];
                    $phone->pl_enabled = true;
                    $phone->pl_title = trim($item['pn_name']);
                    if (!$phone->save()) {
                        Yii::error(
                            VarDumper::dumpAsString([$phone->attributes, $phone->errors]),
                            'PhoneListService:synchronizationPhoneNumbers:PhoneList:save'
                        );
                    } else {
                        $data['created'][] = $phone->pl_phone_number . ' - ' . $phone->pl_title;
                    }
                }
            }
        } else {
            $data['error'] = 'Not found response data';
        }

        return $data;
    }
}
