<?php

use common\models\ClientPhone;
use yii\db\Migration;

/**
 * Class m190807_104601_update_phones_on_db
 */
class m190807_104601_update_phones_on_db extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $countUpdated = 0;
        $countDeleted = 0;
        foreach (ClientPhone::find()->select(['id', 'phone'])->asArray()->batch() as $phones) {
            foreach ($phones as $phone) {
                $lastPhone = preg_replace('~[\D]~', '', $phone['phone']);
                if (!$lastPhone) {
                    ClientPhone::deleteAll(['id' => $phone['id']]);
                    $countDeleted++;
                    continue;
                }
                $newPhone = '+' . $lastPhone;
                if ($newPhone !== $phone['phone']) {
                    ClientPhone::updateAll(['phone' => $newPhone], ['id' => $phone['id']]);
                    $countUpdated++;
                }
            }
        }
        echo 'updated: ' . $countUpdated . ' phones' . PHP_EOL;
        echo 'deleted: ' . $countDeleted . ' phones' . PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190807_104601_update_phones_on_db cannot be reverted.\n";
        return true;
    }
}
