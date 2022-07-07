<?php

namespace modules\objectSegment\src\entities\search;

use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentTask;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ObjectSegmentListSearch extends ObjectSegmentList
{
    public $taskAssigned;
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['osl_id', 'osl_updated_user_id', 'osl_ost_id'], 'integer'],
            [
                [
                    'osl_title',
                    'osl_enabled',
                    'osl_description',
                    'osl_is_system',
                    'taskAssigned',
                ],
                'safe'
            ],
            [
                [
                    'osl_created_dt',
                    'osl_updated_dt'
                ],
                'date',
                'format' => 'php:Y-m-d'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
        $query = ObjectSegmentList::find();

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['osl_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }



        if (!empty($this->taskAssigned)) {
            $ostTableName = ObjectSegmentTask::tableName();
            $query->innerJoin(
                $ostTableName,
                "osl_id = {$ostTableName}.ostl_osl_id",
            )->andWhere([
                'IN', "{$ostTableName}.ostl_tl_id", $this->taskAssigned,
            ]);
        }


        $query->andFilterWhere([
            'osl_id'                                  => $this->osl_id,
            'osl_ost_id'                              => $this->osl_ost_id,
            'osl_description'                         => $this->osl_description,
            'date_format(osl_created_dt, "%Y-%m-%d")' => $this->osl_created_dt,
            'date_format(osl_updated_dt, "%Y-%m-%d")' => $this->osl_updated_dt,
            'osl_updated_user_id'                     => $this->osl_updated_user_id,
            'osl_enabled'                             => $this->osl_enabled,
            'osl_is_system'                           => $this->osl_is_system
        ]);
        $query
            ->andFilterWhere(['like', 'osl_title', $this->osl_title]);

        return $dataProvider;
    }
}
