<?php


namespace sales\viewModel\chat;

use sales\entities\chat\ChatExtendedGraphsSearch;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class ViewModelChatExtendedGraph
 * @package sales\viewmodel\chat
 *
 * @property ChatExtendedGraphsSearch $chatExtendedGraphsSearch
 *
 */
class ViewModelChatExtendedGraph
{
    public array $clientChatData;
    public string $preparedData = '';
    public array $amountOfChats;

    public ChatExtendedGraphsSearch $chatExtendedGraphsSearch;

    /**
     * ViewModelChatGraph constructor.
     * @param ArrayDataProvider $chatData
     * @param ChatExtendedGraphsSearch $chatExtendedGraphsSearch
     */
    public function __construct(ArrayDataProvider $chatData, ChatExtendedGraphsSearch $chatExtendedGraphsSearch)
    {
        $this->clientChatData = $chatData->getModels();
        $this->chatExtendedGraphsSearch = $chatExtendedGraphsSearch;
        $this->getSummaryOfChats();
        $this->formatChatGraphData();
    }

    /**
     * @return void
     */
    private function formatChatGraphData(): void
    {
        $mappedData = [];

        foreach ($this->clientChatData as $arr) {
            array_push($mappedData, [
                $arr['date'],
                (int)$arr['initiatedByClient'],
                //\Yii::$app->formatter->asDuration(floor($arr['sumFrtOfChatsInGroup'] / $arr['initiatedByClient'])),
                (int)$arr['initiatedByAgent'],
                (int)$arr['acceptedByAgent'],
                (int)$arr['missedChats'],

            ]);
        }

        if ($this->chatExtendedGraphsSearch->graphGroupBy === ChatExtendedGraphsSearch::DATE_FORMAT_WEEKDAYS) {
            $mappedData = $this->setWeekDayName($mappedData);
        }

        $headers = [
            'Date',
            'New Incoming Client Chats',
            //['type' => 'string', 'role' => 'tooltip', 'p' => ['html' => true]],
            'New Outgoing Agent Chats',
            'Accepted By Agent Chats',
            'Missed Chats',
        ];
        if ($mappedData) {
            $this->preparedData = json_encode(ArrayHelper::merge([$headers], $mappedData));
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function setWeekDayName(array $data): array
    {
        $week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($data as $key => $arr) {
            $firstKey = array_key_first($arr);
            $data[$key][$firstKey] = $week[$arr[$firstKey]];
        }

        return $data;
    }

    private function getSummaryOfChats()
    {
        $totalInitiatedByClient = array_sum(array_column($this->clientChatData, 'initiatedByClient'));
        $totalInitiatedByAgent = array_sum(array_column($this->clientChatData, 'initiatedByAgent'));
        $totalAcceptedByAgent = array_sum(array_column($this->clientChatData, 'acceptedByAgent'));
        $totalMissedChats = array_sum(array_column($this->clientChatData, 'missedChats'));
        $totalFrtAvg = floor(array_sum(array_column($this->clientChatData, 'sumFrtOfChatsInGroup')) / ($totalInitiatedByClient ?: 1));
        $totalChatDurationAvg = floor(array_sum(array_column($this->clientChatData, 'sumChatDurationInGroup')) / ($totalInitiatedByClient + $totalInitiatedByAgent ?: 1));
        $this->amountOfChats = [
            'clients' => $totalInitiatedByClient,
            'agents' => $totalInitiatedByAgent,
            'total' => $totalInitiatedByClient + $totalInitiatedByAgent,
            'acceptedByAgent' => $totalAcceptedByAgent,
            'missedChats' => $totalMissedChats,
            'totalFrtAvg' => \Yii::$app->formatter->asDuration($totalFrtAvg),
            'totalChatDurationAvg' =>  \Yii::$app->formatter->asDuration($totalChatDurationAvg),
        ];
    }

}