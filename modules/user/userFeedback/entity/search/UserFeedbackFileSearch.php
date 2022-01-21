<?php

namespace modules\user\userFeedback\entity\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\user\userFeedback\entity\UserFeedbackFile;

/**
 * UserFeedbackFileSearch represents the model behind the search form of `modules\user\userFeedback\entity\UserFeedbackFile`.
 */
class UserFeedbackFileSearch extends UserFeedbackFile
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uff_id', 'uff_uf_id', 'uff_size', 'uff_created_user_id'], 'integer'],
            [['uff_mimetype', 'uff_filename', 'uff_title', 'uff_blob', 'uff_created_dt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = UserFeedbackFile::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'uff_id' => $this->uff_id,
            'uff_uf_id' => $this->uff_uf_id,
            'uff_size' => $this->uff_size,
            'uff_created_dt' => $this->uff_created_dt,
            'uff_created_user_id' => $this->uff_created_user_id,
        ]);

        $query->andFilterWhere(['ilike', 'uff_mimetype', $this->uff_mimetype])
            ->andFilterWhere(['ilike', 'uff_filename', $this->uff_filename])
            ->andFilterWhere(['ilike', 'uff_title', $this->uff_title])
            ->andFilterWhere(['ilike', 'uff_blob', $this->uff_blob]);

        return $dataProvider;
    }
}
