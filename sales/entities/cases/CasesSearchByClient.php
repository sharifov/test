<?php

namespace sales\entities\cases;

use sales\access\EmployeeProjectAccess;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class CasesSearchByClient
 *
 * @property $clientId
 */
class CasesSearchByClient extends Model
{

    public $clientId;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['clientId', 'required'],
            ['clientId', 'integer']
        ];
    }

    /**
     * @param $params
     * @param int $userId
     * @return ActiveDataProvider
     */
    public function search($params, $userId): ActiveDataProvider
    {
        $query = Cases::find();

        $query->with('project', 'category', 'owner', 'lead', 'department');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andWhere(['cs_client_id' => $this->clientId]);

        $query->andWhere(['cs_project_id' => array_keys(EmployeeProjectAccess::getProjects($userId))]);

        return $dataProvider;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public static function getShortName(): string
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }

}
