<?php


namespace sales\viewModel\chat;


use sales\entities\chat\ChatFeedbackGraphSearch;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use common\components\ChartTools;

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
    //public array $clientChatFeedbackData;
    public ArrayDataProvider $dataProvider;
    public array $chatData;
    public array $gridColumns;
    public array $prepareStatsData;
    public string $timeRange;

    /**
     * ViewModelChatFeedbackGraph constructor.
     * @param SqlDataProvider $chatData
     * @param ChatFeedbackGraphSearch $chatFeedbackGraphsSearch
     */
    public function __construct(SqlDataProvider $chatData, ChatFeedbackGraphSearch $chatFeedbackGraphsSearch)
    {
        $this->chatData = $chatData->getModels();
        $this->dataProvider = (new ArrayDataProvider(['allModels' => $this->chatData]));
        $this->timeRange = $chatFeedbackGraphsSearch->timeRange;
        $this->formatChatGraphData();
        //var_dump($this->timeRange); die();
    }

    private function formatChatGraphData()
    {
        $timeRange = explode(' - ', $this->timeRange);
        $chartData = ChartTools::getDaysRange($timeRange[0], $timeRange[1]);

        foreach ($chartData as $key => $item) {
            $chartBar = [$item, 0, 0, 0, 0, 0];
            foreach ($this->chatData as $chatData) {
                if (!strcmp($item, date('Y-m-d', strtotime($chatData['ccf_created_dt'])))) {
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

        var_dump($chartData);
    }

}