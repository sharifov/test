<?php

namespace modules\abac\src\entities\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\abac\src\entities\AbacPolicy;
use yii\data\ArrayDataProvider;

/**
 * AbacPolicyImportSearch represents the model behind the search form of `modules\abac\src\entities\AbacPolicy`.
 *
 * @property string|null $object
 * @property array $data
 * @property bool $filtered
 */
class AbacPolicyImportSearch extends Model
{
    public $object;
    public $created_dt;
    public $updated_dt;
    public $action;
    public $effect;
    public $subject;
    public $sort_order;
    public $enabled;
    public $id;
    public $action_id;

    public array $data = [];
    private bool $filtered = false;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['object'], 'string'],
            [['subject', 'action', 'created_dt', 'updated_dt', 'enabled'], 'safe'],
            [['effect', 'sort_order', 'id', 'action_id'], 'integer'],
        ];
    }



    protected function getData(): array
    {
        $data = $this->data;

        if ($this->filtered) {
            $data = array_filter($data, function ($value) {
                $conditions = [true];
                if (!empty($this->object)) {
                    $conditions[] = strpos($value['object'], $this->object) !== false;
                }
                if (!empty($this->subject)) {
                    $conditions[] = strpos($value['subject'], $this->subject) !== false;
                }

                if (!empty($this->action)) {
                    $conditions[] = strpos($value['action'], $this->action) !== false;
                }

                if (!empty($this->sort_order)) {
                    $conditions[] = ($value['sort_order'] == $this->sort_order);
                }
                if (is_numeric($this->enabled)) {
                    $conditions[] = ($value['enabled'] == $this->enabled);
                }
                if (is_numeric($this->effect)) {
                    $conditions[] = ($value['effect'] == $this->effect);
                }

                if (!empty($this->created_dt)) {
                    $conditions[] = strpos($value['created_dt'], $this->created_dt) !== false;
                }

                if (!empty($this->updated_dt)) {
                    $conditions[] = strpos($value['updated_dt'], $this->updated_dt) !== false;
                }

                if (is_numeric($this->id)) {
                    $conditions[] = ($value['id'] == $this->id);
                }

                if (is_numeric($this->action_id)) {
                    $conditions[] = ($value['action_id'] == $this->action_id);
                }

                return array_product($conditions);
            });
        }

        return $data;
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
        if ($this->load($params) && $this->validate()) {
            $this->filtered = true;
        }


        $dataProvider = new ArrayDataProvider([
            'allModels' => $this->getData(),
            'sort' => [
                'defaultOrder' => [
                    'action_id' => SORT_ASC,
                    'sort_order' => SORT_ASC,
                    'enabled' => SORT_DESC,
                ],
                'attributes' => ['action_id', 'sort_order', 'enabled', 'effect', 'created_dt', 'updated_dt', 'object', 'id'],
            ],
            'pagination' => [
                'pageSize' => 10000,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
