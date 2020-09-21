<?php

namespace sales\model\call\entity\callCommand\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\call\entity\callCommand\CallCommand;

/**
 * CallCommandSearch represents the model behind the search form of `sales\model\call\entity\callCommand\CallCommand`.
 */
class CallCommandSearch extends CallCommand
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ccom_id', 'ccom_parent_id', 'ccom_project_id', 'ccom_type_id', 'ccom_sort_order', 'ccom_user_id', 'ccom_created_user_id', 'ccom_updated_user_id'], 'integer'],
            [['ccom_lang_id', 'ccom_name', 'ccom_params_json', 'ccom_created_dt', 'ccom_updated_dt'], 'safe'],
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
        $query = CallCommand::find();

        $query->joinWith('ccomProject');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['ccom_id' => SORT_DESC],
            ],
        ]);

        $dataProvider->sort->attributes['ccom_project_id'] = [
            'asc' => ['projects.name' => SORT_ASC],
            'desc' => ['projects.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ccom_id' => $this->ccom_id,
            'ccom_parent_id' => $this->ccom_parent_id,
            'ccom_project_id' => $this->ccom_project_id,
            'ccom_type_id' => $this->ccom_type_id,
            'ccom_sort_order' => $this->ccom_sort_order,
            'ccom_user_id' => $this->ccom_user_id,
            'ccom_created_user_id' => $this->ccom_created_user_id,
            'ccom_updated_user_id' => $this->ccom_updated_user_id,
            'ccom_updated_dt' => $this->ccom_updated_dt,
        ]);

         if ($this->ccom_created_dt){
            $query->andFilterWhere(['>=', 'ccom_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ccom_created_dt))])
                ->andFilterWhere(['<=', 'ccom_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ccom_created_dt) + 3600 * 24)]);
        }

        $query->andFilterWhere(['like', 'ccom_lang_id', $this->ccom_lang_id])
            ->andFilterWhere(['like', 'ccom_name', $this->ccom_name])
            ->andFilterWhere(['like', 'ccom_params_json', $this->ccom_params_json]);

        return $dataProvider;
    }
}
