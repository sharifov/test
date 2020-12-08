<?php

namespace sales\viewModel\chat;

use common\components\ChartTools;
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
                (int)$arr['acceptedByAgentSourceAgent'] + (int)$arr['acceptedByAgentSourceClient'],
                (int)$arr['missedChats'],

            ]);
        }

        if ($this->chatExtendedGraphsSearch->graphGroupBy == ChatExtendedGraphsSearch::DATE_FORMAT_HOURS) {
            $mappedData = $this->normalizeByHoursOnAxisX($mappedData);
        }

        if ($this->chatExtendedGraphsSearch->graphGroupBy == ChatExtendedGraphsSearch::DATE_FORMAT_DAYS) {
            $mappedData = $this->normalizeByDaysOnAxisX($mappedData);
        }

        if ($this->chatExtendedGraphsSearch->graphGroupBy == ChatExtendedGraphsSearch::DATE_FORMAT_WEEKS) {
            $mappedData = $this->normalizeByWeeksOnAxisX($mappedData);
        }

        if ($this->chatExtendedGraphsSearch->graphGroupBy === ChatExtendedGraphsSearch::DATE_FORMAT_WEEKDAYS) {
            $mappedData = $this->setWeekDayName($mappedData);
        }

        if ($this->chatExtendedGraphsSearch->graphGroupBy === ChatExtendedGraphsSearch::DATE_FORMAT_MONTH) {
            $mappedData = $this->setMonthName($mappedData);
            $mappedData = $this->normalizeByMonthsOnAxisX($mappedData);
        }

        if ($this->chatExtendedGraphsSearch->graphGroupBy == ChatExtendedGraphsSearch::DATE_FORMAT_HOURS_DAYS) {
            $mappedData = $this->normalizeByHoursOfDaysOnAxisX($mappedData);
        }

        if ($this->chatExtendedGraphsSearch->graphGroupBy == ChatExtendedGraphsSearch::DATE_FORMAT_WEEKDAYS) {
            $mappedData = $this->normalizeByWeekDaysOnAxisX($mappedData);
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
    private function normalizeByHoursOnAxisX(array $data): array
    {
        $timeRange = explode(' - ', $this->chatExtendedGraphsSearch->createTimeRange);
        $groupFormat = $this->chatExtendedGraphsSearch::GROUP_FORMAT_DAYS_HOURS;
        $axisX = ChartTools::getHoursRange($timeRange[0], $timeRange[1], $step = '+1 hour', $groupFormat);
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;
        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, 0, 0, 0]);
            }
        }

        usort($normalizedData, function ($firstElement, $secondElement) {
            $datetimeFirst = strtotime($firstElement[0]);
            $datetimeSecond = strtotime($secondElement[0]);
            return $datetimeFirst - $datetimeSecond;
        });

        return $normalizedData;
    }
    /**
     * @param array $data
     * @return array
     */
    private function normalizeByHoursOfDaysOnAxisX(array $data): array
    {
        $axisX = ChartTools::getHourRange();
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;
        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, 0, 0, 0]);
            }
        }

        usort($normalizedData, function ($firstElement, $secondElement) {
            $datetimeFirst = strtotime($firstElement[0]);
            $datetimeSecond = strtotime($secondElement[0]);
            return $datetimeFirst - $datetimeSecond;
        });

        return $normalizedData;
    }

    /**
     * @param array $data
     * @return array
     */
    private function normalizeByWeekDaysOnAxisX(array $data): array
    {
        $axisX = ChartTools::getWeekDaysRange();
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;
        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, 0, 0, 0]);
            }
        }

        usort($normalizedData, function ($a, $b) {
            return date('N', strtotime($a[0])) - date('N', strtotime($b[0]));
        });

        return $normalizedData;
    }

    /**
     * @param array $data
     * @return array
     */
    private function normalizeByDaysOnAxisX(array $data): array
    {
        $timeRange = explode(' - ', $this->chatExtendedGraphsSearch->createTimeRange);
        $axisX = ChartTools::getDaysRange($timeRange[0], $timeRange[1]);
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;
        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, 0, 0, 0]);
            }
        }

        usort($normalizedData, function ($firstElement, $secondElement) {
            $datetimeFirst = strtotime($firstElement[0]);
            $datetimeSecond = strtotime($secondElement[0]);
            return $datetimeFirst - $datetimeSecond;
        });

        return $normalizedData;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    private function normalizeByWeeksOnAxisX(array $data): array
    {
        $timeRange = explode(' - ', $this->chatExtendedGraphsSearch->createTimeRange);
        $axisX = ChartTools::getWeeksRange(new \DateTime($timeRange[0]), new \DateTime($timeRange[1]));
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;
        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, 0, 0, 0]);
            }
        }

        usort($normalizedData, function ($firstElement, $secondElement) {
            $firstCriteria = explode('/', $firstElement[0]);
            $secondCriteria = explode('/', $secondElement[0]);
            $datetimeFirst = strtotime($firstCriteria[0]);
            $datetimeSecond = strtotime($secondCriteria[0]);
            return $datetimeFirst - $datetimeSecond;
        });

        return $normalizedData;
    }

    /**
     * @param array $data
     * @return array
     */
    private function normalizeByMonthsOnAxisX(array $data): array
    {
        $timeRange = explode(' - ', $this->chatExtendedGraphsSearch->createTimeRange);
        $axisX = ChartTools::getMonthsRange($timeRange[0], $timeRange[1], 'Y-F');
        $defaultGroups = array_column($data, 0);
        $normalizedData = $data;

        foreach ($axisX as $point) {
            if (in_array($point, $defaultGroups)) {
                continue;
            } else {
                array_push($normalizedData, [$point, 0, 0, 0, 0]);
            }
        }

        usort($normalizedData, function ($firstElement, $secondElement) {
            $datetimeFirst = strtotime($firstElement[0]);
            $datetimeSecond = strtotime($secondElement[0]);
            return $datetimeFirst - $datetimeSecond;
        });

        return $normalizedData;
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

    /**
     * @param array $data
     * @return array
     */
    private function setMonthName(array $data): array
    {
        foreach ($data as $key => $arr) {
            $firstKey = array_key_first($arr);
            $data[$key][$firstKey] = date('Y-F', strtotime($data[$key][$firstKey]));
        }

        return $data;
    }

    private function getSummaryOfChats()
    {
        $totalInitiatedByClient = array_sum(array_column($this->clientChatData, 'newIncomingClientChats'));
        $totalInitiatedByAgent = array_sum(array_column($this->clientChatData, 'newOutgoingAgentChats'));
        $totalInitiatedByClientClosed = array_sum(array_column($this->clientChatData, 'initByClientClosedArchive'));
        $totalInitiatedByAgentClosed = array_sum(array_column($this->clientChatData, 'initByAgentClosedArchive'));
        $acceptedByAgentSourceAgent = array_sum(array_column($this->clientChatData, 'acceptedByAgentSourceAgent'));
        $acceptedByAgentSourceClient = array_sum(array_column($this->clientChatData, 'acceptedByAgentSourceClient'));
        $totalMissedChats = array_sum(array_column($this->clientChatData, 'missedChats'));
        $totalFrtAvg = floor(array_sum(array_column($this->clientChatData, 'sumFrtOfChatsInGroup')) / ($totalInitiatedByClient ?: 1));

        $totalClientChatDurationAvg = floor(array_sum(array_column($this->clientChatData, 'sumClientChatDurationInGroup')) / ($totalInitiatedByClientClosed ?: 1));
        $totalAgentChatDurationAvg = floor(array_sum(array_column($this->clientChatData, 'sumAgentChatDurationInGroup')) / ($totalInitiatedByAgentClosed ?: 1));

        $totalChatDurationAvg = floor((array_sum(array_column($this->clientChatData, 'sumClientChatDurationInGroup')) + array_sum(array_column($this->clientChatData, 'sumAgentChatDurationInGroup')))  / (($totalInitiatedByClientClosed + $totalInitiatedByAgentClosed) ?: 1));

        $this->amountOfChats = [
            'clients' => $totalInitiatedByClient,
            'agents' => $totalInitiatedByAgent,
            'total' => $totalInitiatedByClient + $totalInitiatedByAgent,
            'acceptedByAgentSourceAgent' => $acceptedByAgentSourceAgent,
            'acceptedByAgentSourceClient' => $acceptedByAgentSourceClient,
            'missedChats' => $totalMissedChats,
            'totalFrtAvg' => \Yii::$app->formatter->asDuration($totalFrtAvg),
            'totalClientChatDurationAvg' => \Yii::$app->formatter->asDuration($totalClientChatDurationAvg),
            'totalAgentChatDurationAvg' => \Yii::$app->formatter->asDuration($totalAgentChatDurationAvg),
            'totalChatDurationAvg' =>  \Yii::$app->formatter->asDuration($totalChatDurationAvg),
        ];
    }
}
