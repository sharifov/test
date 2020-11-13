<?php

namespace sales\model\clientChat\entity\projectConfig\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig;

/**
 * ClientChatProjectConfigSearch represents the model behind the search form of `sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig`.
 */
class ClientChatProjectConfigSearch extends ClientChatProjectConfig
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ccpc_project_id', 'ccpc_enabled', 'ccpc_created_user_id', 'ccpc_updated_user_id'], 'integer'],
            [['ccpc_params_json', 'ccpc_theme_json', 'ccpc_registration_json', 'ccpc_settings_json'], 'safe'],
            [['ccpc_created_dt', 'ccpc_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = ClientChatProjectConfig::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ccpc_project_id' => $this->ccpc_project_id,
            'ccpc_enabled' => $this->ccpc_enabled,
            'ccpc_created_user_id' => $this->ccpc_created_user_id,
            'ccpc_updated_user_id' => $this->ccpc_updated_user_id,
            'DATE(ccpc_created_dt)' => $this->ccpc_created_dt,
            'DATE(ccpc_updated_dt)' => $this->ccpc_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'ccpc_params_json', $this->ccpc_params_json])
            ->andFilterWhere(['like', 'ccpc_theme_json', $this->ccpc_theme_json])
            ->andFilterWhere(['like', 'ccpc_registration_json', $this->ccpc_registration_json])
            ->andFilterWhere(['like', 'ccpc_settings_json', $this->ccpc_settings_json]);

        return $dataProvider;
    }
}
