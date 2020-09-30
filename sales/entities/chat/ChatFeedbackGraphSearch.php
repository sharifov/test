<?php


namespace sales\entities\chat;


use sales\model\clientChatFeedback\entity\ClientChatFeedbackSearch;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;

class ChatFeedbackGraphSearch extends ClientChatFeedbackSearch
{
    public string $timeRange;

    public const DEFAULT_PERIOD = '-6 days';

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->timeRange = date('Y-m-d 00:00:00', strtotime(self::DEFAULT_PERIOD)) . ' - ' . date('Y-m-d 23:59:59');
    }

    public function stats()
    {
        $query = static::find()->joinWith(['clientChat', 'client', 'employee']);
        $query->select('*');

        /*return new ArrayDataProvider([
            'allModels' => $query->createCommand()->queryAll(),
            'pagination' => false,
        ]);*/

        return new SqlDataProvider(['sql' => $query->createCommand()->rawSql, 'pagination' => false]);
    }
}