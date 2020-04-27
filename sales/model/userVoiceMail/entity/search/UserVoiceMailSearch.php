<?php

namespace sales\model\userVoiceMail\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\userVoiceMail\entity\UserVoiceMail;

class UserVoiceMailSearch extends UserVoiceMail
{
    public function rules(): array
    {
        return [
            ['uvm_created_dt', 'safe'],

            ['uvm_created_user_id', 'integer'],

            ['uvm_enabled', 'integer'],

            ['uvm_id', 'integer'],

            ['uvm_max_recording_time', 'integer'],

            ['uvm_name', 'safe'],

            ['uvm_record_enable', 'integer'],

            ['uvm_say_language', 'safe'],

            ['uvm_say_text_message', 'safe'],

            ['uvm_say_voice', 'safe'],

            ['uvm_transcribe_enable', 'integer'],

            ['uvm_updated_dt', 'safe'],

            ['uvm_updated_user_id', 'integer'],

            ['uvm_user_id', 'integer'],

            ['uvm_voice_file_message', 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'uvm_id' => $this->uvm_id,
            'uvm_user_id' => $this->uvm_user_id,
            'uvm_record_enable' => $this->uvm_record_enable,
            'uvm_max_recording_time' => $this->uvm_max_recording_time,
            'uvm_transcribe_enable' => $this->uvm_transcribe_enable,
            'uvm_enabled' => $this->uvm_enabled,
            'uvm_created_dt' => $this->uvm_created_dt,
            'uvm_updated_dt' => $this->uvm_updated_dt,
            'uvm_created_user_id' => $this->uvm_created_user_id,
            'uvm_updated_user_id' => $this->uvm_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'uvm_name', $this->uvm_name])
            ->andFilterWhere(['like', 'uvm_say_text_message', $this->uvm_say_text_message])
            ->andFilterWhere(['like', 'uvm_say_language', $this->uvm_say_language])
            ->andFilterWhere(['like', 'uvm_say_voice', $this->uvm_say_voice])
            ->andFilterWhere(['like', 'uvm_voice_file_message', $this->uvm_voice_file_message]);

        return $dataProvider;
    }
}
