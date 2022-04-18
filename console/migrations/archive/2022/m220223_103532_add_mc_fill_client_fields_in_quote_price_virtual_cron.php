<?php

use kivork\VirtualCron\Models\CronScheduler;
use kivork\VirtualCron\Services\Hasher;
use yii\db\Migration;

/**
 * Class m220223_103532_add_mc_fill_client_fields_in_quote_price_virtual_cron
 */
class m220223_103532_add_mc_fill_client_fields_in_quote_price_virtual_cron extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cronScheduler = new CronScheduler();
            $cronScheduler->cs_cron_expression = '*/10 * * * *';
            $cronScheduler->cs_cron_command = 'one-time/fill-client-price-fields-in-quote-price';
            $cronScheduler->cs_description = 'One-time: Multicurrency Fill Client Price Fields In Quote Price';
            $cronScheduler->cs_hash = Hasher::generate(
                $cronScheduler->cs_cron_expression,
                $cronScheduler->cs_cron_command,
                $cronScheduler->cs_cron_params
            );
            $cronScheduler->cs_enabled = false;

            if (!$cronScheduler->save()) {
                throw new \RuntimeException(\src\helpers\ErrorsToStringHelper::extractFromModel($cronScheduler, ' '));
            }
            echo ' - Hash: ' . $cronScheduler->cs_hash . PHP_EOL;
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220223_103532_add_mc_fill_client_fields_in_quote_price_virtual_cron:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $cronScheduler = CronScheduler::find()->where(['cs_cron_command' => 'one-time/fill-client-price-fields-in-quote-price'])->one();
            if ($cronScheduler) {
                if ($cronScheduler->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cronScheduler->errors, 'm220223_103532_add_mc_fill_client_fields_in_quote_price_virtual_cron:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220223_103532_add_mc_fill_client_fields_in_quote_price_virtual_cron:safeDown:Throwable');
        }
    }
}
