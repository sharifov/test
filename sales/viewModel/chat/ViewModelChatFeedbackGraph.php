<?php


namespace sales\viewModel\chat;


use sales\entities\chat\ChatFeedbackGraphSearch;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use common\components\ChartTools;
use yii\helpers\ArrayHelper;
use common\models\Employee;

/**
 * Class ViewModelChatFeedbackGraph
 * @package sales\viewmodel\chat
 *
 * @property array $chatData
 * @property ChatFeedbackGraphSearch $chatFeedbackGraphsSearch
 * @property ArrayDataProvider $dataProvider
 * @property array $gridColumns
 */
class ViewModelChatFeedbackGraph
{
    public ChatFeedbackGraphSearch $chatFeedbackGraphsSearch;
    public ArrayDataProvider $dataProvider;
    public array $chatData;
    public array $gridColumns;
    public string $prepareStatsData;
    public string $timeRange;

    /**
     * ViewModelChatFeedbackGraph constructor.
     * @param SqlDataProvider $chatData
     * @param ChatFeedbackGraphSearch $chatFeedbackGraphsSearch
     */
    public function __construct(SqlDataProvider $chatData, ChatFeedbackGraphSearch $chatFeedbackGraphsSearch)
    {
        $this->chatData = $chatData->getModels();
        $this->chatFeedbackGraphsSearch = $chatFeedbackGraphsSearch;
        $this->dataProvider = (new ArrayDataProvider([
            'allModels' => $this->chatData,
            'pagination' => [
                'pageSize' => 10
            ]
        ]));
        $this->dataProvider->setSort([
            'attributes' => [
                'ccf_id' => [
                    'asc' => ['ccf_id' => SORT_ASC],
                    'desc' => ['ccf_id' => SORT_DESC],
                ],
                'ccf_client_id' => [
                    'asc' => ['ccf_id' => SORT_ASC],
                    'desc' => ['ccf_id' => SORT_DESC],
                ],
                'ccf_user_id' => [
                    'asc' => ['ccf_user_id' => SORT_ASC],
                    'desc' => ['ccf_user_id' => SORT_DESC],
                ],
                'ccf_rating' => [
                    'asc' => ['ccf_rating' => SORT_ASC],
                    'desc' => ['ccf_rating' => SORT_DESC],
                ],
                'ccf_message' => [
                    'asc' => ['ccf_message' => SORT_ASC],
                    'desc' => ['ccf_message' => SORT_DESC],
                ],
                'ccf_created_dt' => [
                    'asc' => ['ccf_message' => SORT_ASC],
                    'desc' => ['ccf_message' => SORT_DESC],
                ],
            ],
            'defaultOrder' => [
                'ccf_id' => SORT_DESC
            ]
        ]);

        $this->formatChatGraphData();
    }

    private function formatChatGraphData()
    {
        $timeRange = explode(' - ', $this->chatFeedbackGraphsSearch->timeRange);

        if ($this->chatFeedbackGraphsSearch->groupBy == $this->chatFeedbackGraphsSearch::GROUP_BY_DAYS){
            $groupFormat = $this->chatFeedbackGraphsSearch::GROUP_FORMAT_DAYS;
            $chartData = ChartTools::getDaysRange($timeRange[0], $timeRange[1]);
        } elseif ($this->chatFeedbackGraphsSearch->groupBy == $this->chatFeedbackGraphsSearch::GROUP_BY_HOURS){
            $groupFormat = $this->chatFeedbackGraphsSearch::GROUP_FORMAT_HOURS;
            $chartData = ChartTools::getHoursRange($timeRange[0], $timeRange[1], $step = '+1 hour', $groupFormat);
        } elseif ($this->chatFeedbackGraphsSearch->groupBy == $this->chatFeedbackGraphsSearch::GROUP_BY_WEEKS) {
            $chartData = ChartTools::getWeeksRange(new \DateTime($timeRange[0]), new \DateTime($timeRange[1]));
        } elseif ($this->chatFeedbackGraphsSearch->groupBy == $this->chatFeedbackGraphsSearch::GROUP_BY_MONTH) {
            $groupFormat = $this->chatFeedbackGraphsSearch::GROUP_FORMAT_MONTH;
            $chartData = ChartTools::getMonthsRange($timeRange[0], $timeRange[1]);
        }

        /** @var $chartData */
        foreach ($chartData as $key => $item) {
            $chartBar = [$item, 0, 0, 0, 0, 0];
            foreach ($this->chatData as $chatData) {
                $feedbackCreated = Employee::convertTimeFromUtcToUserTime($this->chatFeedbackGraphsSearch->timeZone, strtotime($chatData['ccf_created_dt']));
                /** @var string $groupFormat */
                if (
                    $this->chatFeedbackGraphsSearch->groupBy == $this->chatFeedbackGraphsSearch::GROUP_BY_HOURS ||
                    $this->chatFeedbackGraphsSearch->groupBy == $this->chatFeedbackGraphsSearch::GROUP_BY_DAYS ||
                    $this->chatFeedbackGraphsSearch->groupBy == $this->chatFeedbackGraphsSearch::GROUP_BY_MONTH
                ) {
                    $mergeCondition = !strcmp($item, date($groupFormat, strtotime($feedbackCreated)));
                } elseif ($this->chatFeedbackGraphsSearch->groupBy == $this->chatFeedbackGraphsSearch::GROUP_BY_WEEKS) {
                    $weekRange = explode('/', $item);
                    $mergeCondition = (strtotime($weekRange[0] . '00:00:00') <= strtotime($feedbackCreated)) && (strtotime($feedbackCreated) <= strtotime($weekRange[1] . ' 23:59:59'));
                }

                if ($mergeCondition) {
                    switch ($chatData['ccf_rating']) {
                        case 1:
                            ++$chartBar[1];
                            break;
                        case 2:
                            ++$chartBar[2];
                            break;
                        case 3:
                            ++$chartBar[3];
                            break;
                        case 4:
                            ++$chartBar[4];
                            break;
                        case 5:
                            ++$chartBar[5];
                            break;
                    }
                }
            }
            $chartData[$key] = $chartBar;
        }

        $headers = [
            'Date',
            'One Start',
            'Two Starts',
            'Three Starts',
            'Four Starts',
            'Five Starts',
        ];

        if ($chartData) {
            $this->prepareStatsData = json_encode(ArrayHelper::merge([$headers], $chartData));
        }
    }

}