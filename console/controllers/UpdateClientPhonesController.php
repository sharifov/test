<?php

namespace console\controllers;

use common\models\ClientPhone;
use yii\console\Controller;

class UpdateClientPhonesController extends Controller
{

    /**
     * Remove spaces from phones
     */
    public function actionRemoveSpaces(): void
    {
        $countUpdated = 0;
        foreach (ClientPhone::find()->select(['id', 'phone'])->asArray()->batch() as $phones) {
            foreach ($phones as $phone) {
                $newPhone =  str_replace([' ', '	'], '', $phone['phone']);
                if ($newPhone !== $phone['phone']) {
                    ClientPhone::updateAll(['phone' => $newPhone], ['id' => $phone['id']]);
//                    echo $phone['phone'] . ' -> ' . $newPhone . PHP_EOL;
                    $countUpdated++;
                }
            }
        }
        echo 'updated: ' . $countUpdated . ' phones' . PHP_EOL;
    }

}
