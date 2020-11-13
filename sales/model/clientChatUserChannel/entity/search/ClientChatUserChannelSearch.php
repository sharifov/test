<?php

namespace sales\model\clientChatUserChannel\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use yii\db\Expression;

class ClientChatUserChannelSearch extends ClientChatUserChannel
{
    public function rules(): array
    {
        return [
            ['ccuc_channel_id', 'integer'],

            [['ccuc_created_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['ccuc_created_user_id', 'integer'],

            ['ccuc_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['ccuc_created_dt' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ccuc_user_id' => $this->ccuc_user_id,
            'ccuc_channel_id' => $this->ccuc_channel_id,
            'DATE(ccuc_created_dt)' => $this->ccuc_created_dt,
            'ccuc_created_user_id' => $this->ccuc_created_user_id,
        ]);

        return $dataProvider;
    }

    public function searchByUser(array $params)
	{
		$query = static::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		if (!$this->validate()) {
			$query->where('0=1');
			return $dataProvider;
		}

		$query->andFilterWhere([
			'ccc_user_id' => $this->ccuc_user_id
		]);

		return $dataProvider;
	}

	/**
	 * @param int $channelId
	 * @return array|\yii\db\ActiveRecord[]
	 */
	public function getAvailableAgentForTransfer(int $channelId): array
	{
		$query = self::find()->select([
			'user_id' => 'ccuc_user_id',
			new Expression('if (nickname_client_chat is null or nickname_client_chat = \'\', username, nickname_client_chat) as `nickname`')
		]);

		$query->byChannelId($channelId);
		$query->joinUser();
		$query->hasRcProfile();
		$query->onlineUsers();

		return $query->asArray()->all();
	}
}
