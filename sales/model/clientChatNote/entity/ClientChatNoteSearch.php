<?php

namespace sales\model\clientChatNote\entity;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\clientChatNote\entity\ClientChatNote;

/**
 * ClientChatNoteSearch
 */
class ClientChatNoteSearch extends ClientChatNote
{

    public function rules(): array
    {
        return [
            [['ccn_id', 'ccn_chat_id', 'ccn_user_id', 'ccn_deleted'], 'integer'],
            [['ccn_note', 'ccn_created_dt', 'ccn_updated_dt'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ClientChatNote::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ccn_id' => $this->ccn_id,
            'ccn_chat_id' => $this->ccn_chat_id,
            'ccn_user_id' => $this->ccn_user_id,
            'ccn_deleted' => $this->ccn_deleted,
            'ccn_created_dt' => $this->ccn_created_dt,
            'ccn_updated_dt' => $this->ccn_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'ccn_note', $this->ccn_note]);

        return $dataProvider;
    }
}
