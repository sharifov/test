<?php

use common\models\DepartmentPhoneProject;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m201111_121428_add_default_repeat_params_to_dep_phones
 */
class m201111_121428_add_default_repeat_params_to_dep_phones extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $log = [];
        /** @var DepartmentPhoneProject[] $phones */
        $phones = DepartmentPhoneProject::find()->all();
        foreach ($phones as $phone) {
            $params = [];
            if ($phone->dpp_params) {
                $params = json_decode($phone->dpp_params, true);
            }
            $params['queue_repeat'] = [
                'enable' => false,
                'repeat_time' => 180,
                'language' => 'en-US',
                'voice' => 'Polly.Joanna',
                'say' => 'Please hold, while you are connected to the next available agent.',
                'play' => 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3',
            ];
            $phone->dpp_params = json_encode($params);
            if (!$phone->save()) {
                $log[] = $phone->getErrors();
            }
        }
        if ($log) {
            VarDumper::dump($log);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $log = [];
        /** @var DepartmentPhoneProject[] $phones */
        $phones = DepartmentPhoneProject::find()->all();
        foreach ($phones as $phone) {
            $params = [];
            if ($phone->dpp_params) {
                $params = json_decode($phone->dpp_params, true);
            }
            if (!$params) {
                continue;
            }
            if (empty($params['queue_repeat'])) {
                continue;
            }
            unset($params['queue_repeat']);
            $phone->dpp_params = json_encode($params);
            if (!$phone->save()) {
                $log[] = $phone->getErrors();
            }
        }
        if ($log) {
            VarDumper::dump($log);
        }
    }
}
