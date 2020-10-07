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
                (int)$arr['newIncomingClientChats'],
                //\Yii::$app->formatter->asDuration(floor($arr['sumFrtOfChatsInGroup'] / $arr['initiatedByClient'])),
                (int)$arr['newOutgoingAgentChats'],
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
        $totalInitiatedByClient = array_sum(array_column($this->clientChatData, 'newIncomingClientChats'));
        $totalInitiatedByAgent = array_sum(array_column($this->clientChatData, 'newOutgoingAgentChats'));
        $totalInitiatedByClientClosed = array_sum(array_column($this->clientChatData, 'initiatedByClientClosed'));
        $totalInitiatedByAgentClosed = array_sum(array_column($this->clientChatData, 'initiatedByAgentClosed'));
        $totalAcceptedByAgent = array_sum(array_column($this->clientChatData, 'acceptedByAgent'));
        $totalMissedChats = array_sum(array_column($this->clientChatData, 'missedChats'));
        $totalFrtAvg = floor(array_sum(array_column($this->clientChatData, 'sumFrtOfChatsInGroup')) / ($totalInitiatedByClient ?: 1));

        $totalClientChatDurationAvg = floor(array_sum(array_column($this->clientChatData, 'sumClientChatDurationInGroup')) / ($totalInitiatedByClientClosed ?: 1));
        $totalAgentChatDurationAvg = floor(array_sum(array_column($this->clientChatData, 'sumAgentChatDurationInGroup')) / ($totalInitiatedByAgentClosed ?: 1));

        $totalChatDurationAvg = floor((array_sum(array_column($this->clientChatData, 'sumClientChatDurationInGroup')) + array_sum(array_column($this->clientChatData, 'sumAgentChatDurationInGroup')))  / (($totalInitiatedByClientClosed + $totalInitiatedByAgentClosed) ?: 1));

        $this->amountOfChats = [
            'clients' => $totalInitiatedByClient,
            'agents' => $totalInitiatedByAgent,
            'total' => $totalInitiatedByClient + $totalInitiatedByAgent,
            'acceptedByAgent' => $totalAcceptedByAgent,
            'missedChats' => $totalMissedChats,
            'totalFrtAvg' => \Yii::$app->formatter->asDuration($totalFrtAvg),
            'totalClientChatDurationAvg' => \Yii::$app->formatter->asDuration($totalClientChatDurationAvg),
            'totalAgentChatDurationAvg' => \Yii::$app->formatter->asDuration($totalAgentChatDurationAvg),
            'totalChatDurationAvg' =>  \Yii::$app->formatter->asDuration($totalChatDurationAvg),
        ];
    }

}