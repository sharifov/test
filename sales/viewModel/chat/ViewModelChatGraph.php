<?php


namespace sales\viewModel\chat;

use sales\entities\chat\ChatGraphsSearch;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class ViewModelChatGraph
 * @package sales\viewmodel\call
 *
 * @property ChatGraphsSearch $chatGraphsSearch
 *
 */

class ViewModelChatGraph
{
    public string $preparedData = '';
    public array $clientChatData;
    public ChatGraphsSearch $chatGraphsSearch;

    /**
     * ViewModelChatGraph constructor.
     * @param SqlDataProvider $chatData
     * @param ChatGraphsSearch $chatGraphsSearch
     */
    public function __construct(SqlDataProvider $chatData, ChatGraphsSearch $chatGraphsSearch)
    {
        $this->clientChatData = $chatData->getModels();
        $this->chatGraphsSearch = $chatGraphsSearch;
        $this->formatChatGraphData();
    }

    /**
     * @return void
     */
    private function formatChatGraphData(): void
    {
        $mappedData = [];

        foreach ($this->clientChatData as $arr){
            array_push($mappedData, [
                $arr['date'],
                (int)$arr['new'],
                (int)$arr['pending'],
                (int)$arr['progress'],
                (int)$arr['transfer'],
                (int)$arr['hold'],
                (int)$arr['idle'],
                (int)$arr['closed']
            ]);
        }

        if ($this->chatGraphsSearch->graphGroupBy === ChatGraphsSearch::DATE_FORMAT_WEEKDAYS) {
            $mappedData = $this->setWeekDayName($mappedData);
        }

        $headers = [
            'Date',
            'New',
            'Pending',
            'Progress',
            'Transfer',
            'Hold',
            'Idle',
            'Closed',
        ];
        if ($mappedData){
            $this->preparedData = json_encode(ArrayHelper::merge([$headers], $mappedData));
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function setWeekDayName(array $data):array
    {
        $week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($data as $key => $arr){
            $firstKey = array_key_first($arr);
            $data[$key][$firstKey] = $week[$arr[$firstKey]];
        }

        return $data;
    }
}