<?php

use sales\model\project\entity\params\CallParams;
use sales\model\project\entity\params\ObjectParams;
use sales\model\project\entity\params\Params;
use sales\model\project\entity\params\SmsParams;
use sales\model\project\entity\params\StyleParams;
use yii\helpers\Json;
use common\models\Project;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m210104_151733_add_column_p_params_json_tbl_projects
 */
class m210104_151733_add_column_p_params_json_tbl_projects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%projects}}', 'p_params_json', $this->json());

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        foreach (Project::find()->all() as $project) {
            /** @var Project $project */
            if (!$project->custom_data) {
                $project->p_params_json = Params::default();
            } else {
                try {
                    $customData = Json::decode($project->custom_data);
                    $style = StyleParams::default();
                    if (array_key_exists('background-color', $customData)) {
                        $style['background-color'] = $customData['background-color'];
                    }
                    if (array_key_exists('color', $customData)) {
                        $style['color'] = $customData['color'];
                    }
                    $sms = SmsParams::default();
                    if (array_key_exists('sms_enabled', $customData)) {
                        $sms['sms_enabled'] = $customData['sms_enabled'];
                    }
                    $object = ObjectParams::default();
                    if (array_key_exists('object', $customData)) {
                        $object = $customData['object'];
                    }
                    $call = CallParams::default();
                    if (array_key_exists('call_recording_disabled', $customData)) {
                        $call['call_recording_disabled'] = $customData['call_recording_disabled'];
                    }
                    if (array_key_exists('url_say_play_hold', $customData)) {
                        $call['url_say_play_hold'] = $customData['url_say_play_hold'];
                    }
                    if (array_key_exists('url_music_play_hold', $customData)) {
                        $call['url_music_play_hold'] = $customData['url_music_play_hold'];
                    }
                    if (array_key_exists('play_direct_message', $customData)) {
                        $call['play_direct_message'] = $customData['play_direct_message'];
                    }
                    if (array_key_exists('play_redirect_message', $customData)) {
                        $call['play_redirect_message'] = $customData['play_redirect_message'];
                    }
                    if (array_key_exists('say_direct_message', $customData)) {
                        $call['say_direct_message'] = $customData['say_direct_message'];
                    }
                    if (array_key_exists('say_redirect_message', $customData)) {
                        $call['say_redirect_message'] = $customData['say_redirect_message'];
                    }
                    $project->p_params_json = [
                        'style' => $style,
                        'object' => $object,
                        'call' => $call,
                        'sms' => $sms,
                    ];
                } catch (Throwable $e) {
                    echo 'ProjectId: ' . $project->id . ' Error: ' . $e->getMessage() . PHP_EOL;
                    $project->p_params_json = Params::default();
                }
            }
            if (!$project->save()) {
                VarDumper::dump($project->getErrors());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%projects}}', 'p_params_json');
    }
}
