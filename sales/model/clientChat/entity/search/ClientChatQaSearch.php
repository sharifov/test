<?php

namespace sales\model\clientChat\entity\search;

use sales\model\clientChatData\entity\ClientChatData;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\clientChat\entity\ClientChat;
use yii\helpers\ArrayHelper;

/**
 * ClientChatQaSearch represents the model behind the search form of `ClientChat`.
 *
 * @property $createdRangeDate
 * @property string|null $dataCountry
 * @property string|null $dataCity
 * @property int|null $messageBy
 * @property string|null $messageText
 */
class ClientChatQaSearch extends ClientChat
{
    public $createdRangeDate;
    public $dataCountry;
    public $dataCity;
    public $messageBy;
    public $messageText;

    public const MESSAGE_BY_CLIENT = 1;
    public const MESSAGE_BY_USER = 2;

    public const MESSAGE_BY_LIST = [
        self::MESSAGE_BY_CLIENT => 'Client',
        self::MESSAGE_BY_USER=> 'User',
    ];

    public function rules(): array
    {
        return [
            [
                [
                    'cch_id', 'cch_ccr_id', 'cch_project_id',
                    'cch_dep_id', 'cch_channel_id', 'cch_client_id',
                    'cch_owner_user_id', 'cch_case_id', 'cch_lead_id',
                    'cch_status_id', 'cch_ua', 'cch_created_user_id',
                    'cch_updated_user_id', 'cch_client_online', 'messageBy',
                ],
                'integer'
            ],
            [
                [
                    'cch_rid', 'cch_title', 'cch_description',
                    'cch_note', 'cch_ip', 'cch_language_id',
                    'cch_created_dt', 'cch_updated_dt', 'createdRangeDate',
                ],
                'safe'
            ],
            [['dataCountry', 'dataCity', 'messageText'], 'string', 'max' => 100],
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
        $query = ClientChat::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cch_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cch_id' => $this->cch_id,
            'cch_ccr_id' => $this->cch_ccr_id,
            'cch_project_id' => $this->cch_project_id,
            'cch_dep_id' => $this->cch_dep_id,
            'cch_channel_id' => $this->cch_channel_id,
            'cch_client_id' => $this->cch_client_id,
            'cch_owner_user_id' => $this->cch_owner_user_id,
            'cch_case_id' => $this->cch_case_id,
            'cch_lead_id' => $this->cch_lead_id,
            'cch_status_id' => $this->cch_status_id,
            'cch_ua' => $this->cch_ua,
            'cch_created_dt' => $this->cch_created_dt,
            'cch_updated_dt' => $this->cch_updated_dt,
            'cch_created_user_id' => $this->cch_created_user_id,
            'cch_updated_user_id' => $this->cch_updated_user_id,
            'cch_client_online' => $this->cch_client_online,
        ]);

        $query->andFilterWhere(['like', 'cch_rid', $this->cch_rid])
            ->andFilterWhere(['like', 'cch_title', $this->cch_title])
            ->andFilterWhere(['like', 'cch_description', $this->cch_description])
            ->andFilterWhere(['like', 'cch_note', $this->cch_note])
            ->andFilterWhere(['like', 'cch_ip', $this->cch_ip])
            ->andFilterWhere(['like', 'cch_language_id', $this->cch_language_id]);

        if ($this->createdRangeDate) {
			$dateRange = explode(' - ', $this->createdRangeDate);
			if ($dateRange[0] && $dateRange[1]) {
				$fromDate = date('Y-m-d', strtotime($dateRange[0]));
				$toDate = date('Y-m-d', strtotime($dateRange[1]));
				$query->andWhere(['BETWEEN', 'DATE(cch_created_dt)', $fromDate, $toDate]);
			}
		}
		if ($this->dataCountry) {
            $query->andWhere(['cch_id' =>
                ClientChatData::find()->select('ccd_cch_id')->andWhere(['ccd_country' => $this->dataCountry])]);
        }
        if ($this->dataCity) {
            $query->andWhere(['cch_id' =>
                ClientChatData::find()->select('ccd_cch_id')->andWhere(['ccd_city' => $this->dataCity])]);
        }
        if ($this->messageText) {
            $by = ArrayHelper::isIn($this->messageBy, self::MESSAGE_BY_LIST) ? $this->messageBy : null;
            $query->andWhere(['cch_id' => self::getIdsByChatMessage($this->messageText, $by)]);
        }

        return $dataProvider;
    }

    public static function getIdsByCreatorType(int $by): array
    {
        /* TODO::  */

        $sql = 'SELECT 
                    ccm_cch_id
                FROM
                    client_chat_message';

        if ($by === ClientChatQaSearch::MESSAGE_BY_CLIENT) {
            $sql .= ' WHERE ccm_client_id IS NOT NULL';
        } elseif ($by === ClientChatQaSearch::MESSAGE_BY_USER) {
            $sql .= ' WHERE ccm_user_id IS NOT NULL';
        }
        $sql .= ' GROUP BY ccm_cch_id';

        return ArrayHelper::getColumn(
            Yii::$app->db_postgres->createCommand($sql)->queryAll(),
        'ccm_cch_id');
    }

    public static function getIdsByChatMessage(string $msg, ?int $by = null): array
    {
        $sql = "SELECT 
                    ccm_cch_id
                FROM
                    client_chat_message
                WHERE 
                    ccm_body->>'msg' LIKE :msg";

        if ($by === ClientChatQaSearch::MESSAGE_BY_CLIENT) {
            $sql .= ' AND ccm_client_id IS NOT NULL';
        } elseif ($by === ClientChatQaSearch::MESSAGE_BY_USER) {
            $sql .= ' AND ccm_user_id IS NOT NULL';
        }
        $sql .= ' GROUP BY ccm_cch_id';

        return ArrayHelper::getColumn(
            Yii::$app->db_postgres->createCommand(
                $sql,
                [':msg' => '%' . $msg . '%']
            )->queryAll(),
        'ccm_cch_id');
    }
}
