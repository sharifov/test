<?php

namespace src\viewModel\userFeedback;

use modules\user\userFeedback\entity\search\UserFeedbackSearch;
use modules\user\userFeedback\entity\UserFeedback;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;

class ViewModelUserFeedbackGraph
{
    public $totalGraphData;

    public $userFeedbackSearch;

    public $userFeedbackData = 0;

    public $groupsCount;

    public $dataProvider;

    public $gridColumns;

    /**
     * ViewModelTotalCallGraph constructor.
     * @param SqlDataProvider $userFeedbackData
     * @param UserFeedbackSearch $userFeedbackSearch
     * @throws \Exception
     */
    public function __construct(
        SqlDataProvider $userFeedbackData,
        UserFeedbackSearch $userFeedbackSearch
    ) {
        $this->userFeedbackData = $userFeedbackData->getModels();
        $this->userFeedbackSearch = $userFeedbackSearch;

        $this->formatTotalGraphData();

        $this->dataProvider = (new ArrayDataProvider(['allModels' => $this->userFeedbackData]));
//        $this->gridColumns = $this->fetchGridColumns();
    }

    private function formatTotalGraphData()
    {
        $mappedData = [];
        $finalData = [];
        $ci = [];


        foreach ($this->userFeedbackData as $rowIndex) {
            foreach (UserFeedback::getTypeList() as $key => $type) {
                $ci[$key] = 0;
            }

            foreach ($this->userFeedbackData as $row) {
                if ($rowIndex['date'] == $row['date']) {
                    $ci['date'] = $row['date'];


                    $ci[$row['type']] = $row['totalFeedbackCnt'];
                    $finalData[$row['date']] = $ci;
                }
            }
        }

        foreach ($finalData as $arr) {
            foreach ($arr as $key => $value) {
                $userFeedbackTypeName = UserFeedback::getTypeList()[$key] ?? null;
                if ($userFeedbackTypeName) {
                    $data[] = $value;
                    $data[] = $userFeedbackTypeName . ': ' . $value;
                }
            }
            array_unshift($data, $arr['date']);
            $mappedData[] = $data;
            $data = [];
        }



        $data = [
            'Date',
        ];
        foreach (UserFeedback::getTypeList() as $type) {
            $data[] = $type;
            $data[] = [
                'type' => 'string',
                'role' => 'tooltip'
            ];
        }

        $this->totalGraphData = json_encode(ArrayHelper::merge([$data], $mappedData));
    }
}
