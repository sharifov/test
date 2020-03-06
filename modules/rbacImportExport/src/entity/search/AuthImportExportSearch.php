<?php

namespace modules\rbacImportExport\src\entity\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\rbacImportExport\src\entity\AuthImportExport;

/**
 * AuthImportExportSearch represents the model behind the search form of `modules\rbacImportExport\src\entity\AuthImportExport`.
 */
class AuthImportExportSearch extends AuthImportExport
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['aie_id', 'aie_type', 'aie_cnt_roles', 'aie_cnt_permissions', 'aie_cnt_rules', 'aie_cnt_childs', 'aie_file_size', 'aie_user_id'], 'integer'],
            [['aie_file_name', 'aie_created_dt', 'aie_data_json'], 'safe'],
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
        $query = AuthImportExport::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['aie_id' => SORT_DESC]],
			'pagination' => [
				'pageSize' => 30,
			],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'aie_id' => $this->aie_id,
            'aie_type' => $this->aie_type,
            'aie_cnt_roles' => $this->aie_cnt_roles,
            'aie_cnt_permissions' => $this->aie_cnt_permissions,
            'aie_cnt_rules' => $this->aie_cnt_rules,
            'aie_cnt_childs' => $this->aie_cnt_childs,
            'aie_file_size' => $this->aie_file_size,
            'aie_created_dt' => $this->aie_created_dt,
            'aie_user_id' => $this->aie_user_id,
        ]);

        $query->andFilterWhere(['like', 'aie_file_name', $this->aie_file_name])
            ->andFilterWhere(['like', 'aie_data_json', $this->aie_data_json]);

        return $dataProvider;
    }
}
