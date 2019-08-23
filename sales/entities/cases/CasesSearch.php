<?php

namespace sales\entities\cases;

use common\models\CaseSale;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Lead;
use yii\data\ActiveDataProvider;

/**
 * Class CasesSearch
 */
class CasesSearch extends Cases
{

    public $cssSaleId;
    public $cssBookId;
    public $salePNR;
    public $clientPhone;
    public $clientEmail;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['cs_gid', 'string'],
            ['cs_id', 'integer'],
            ['cs_subject', 'string'],
            ['cs_category', 'string'],
            ['cs_status', 'integer'],
            ['cs_user_id', 'string'],
            ['cs_lead_id', 'string'],
            ['cs_dep_id', 'safe'],
            ['cs_created_dt', 'string'],
            ['cs_client_id', 'integer'],
            ['cs_project_id', 'safe'],
            ['cssSaleId', 'integer'],
            [['cssBookId', 'salePNR', 'clientPhone', 'clientEmail'], 'string']

        ];
    }

    /**
     * @param $params
     * @param $isAgent
     * @return ActiveDataProvider
     */
    public function search($params, $isAgent): ActiveDataProvider
    {
        $query = Cases::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cs_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (empty($params) && $isAgent === true){
            $query->where('0=1');
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cs_client_id) {
            $query->andWhere(['cs_client_id' => $this->cs_client_id]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cs_id' => $this->cs_id,
            'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_category' => $this->cs_category,
            'cs_status' => $this->cs_status,
            'cs_dep_id' => $this->cs_dep_id,
        ]);

        if ($this->cs_user_id) {
            $query->andWhere(['cs_user_id' => Employee::find()->select('id')->andWhere(['like', 'username', $this->cs_user_id])]);
        }

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cssSaleId){
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $this->cssSaleId])]);
        }

        if ($this->cssBookId){
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_book_id' => $this->cssBookId])]);
        }

        if ($this->salePNR){
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_pnr' => $this->salePNR])]);
        }

        if ($this->clientPhone){
            $query->andWhere(['cs_client_id' => ClientPhone::find()->select('client_id')->andWhere(['phone' => $this->clientPhone])]);
        }
        if ($this->clientEmail){
            $query->andWhere(['cs_client_id' => ClientEmail::find()->select('client_id')->andWhere(['email' => $this->clientEmail])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);

        return $dataProvider;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cs_id' => 'ID',
            'cs_gid' => 'GID',
            'cs_project_id' => 'Project',
            'cs_subject' => 'Subject',
            'cs_category' => 'Category',
            'cs_status' => 'Status',
            'cs_user_id' => 'User',
            'cs_lead_id' => 'Lead',
            'cs_dep_id' => 'Department',
            'cs_created_dt' => 'Created',
            'cssSaleId' => 'Sale ID',
            'cssBookId' => 'Booking ID',
            'salePNR' => 'Sale PNR'
        ];
    }
}
