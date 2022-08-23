<?php

namespace modules\user\userActivity\forms;

use common\components\validators\CheckJsonValidator;
use kartik\daterange\DateRangeBehavior;
use modules\user\userFeedback\entity\UserFeedback;
use yii\base\Model;

class DashboardSearchForm extends Model
{
    public string $dateTimeRange = '';
    public string $createTimeStart = '';
    public string $createTimeEnd = '';

    public string $clientStartDate = '';
    public string $clientEndDate = '';
//    public string $startedDateRange = '';
//    public string $endedDateRange = '';

    private string $defaultDTStart;
    private string $defaultDTEnd;

    public function __construct(int $defaultMonth = 1, string $formatDt = 'Y-m-d', array $config = [])
    {
        $this->defaultDTEnd = (new \DateTime())->format($formatDt);
        $this->defaultDTStart = (new \DateTimeImmutable())
            ->modify('-' . abs($defaultMonth) . ' months')->format($formatDt);

        parent::__construct($config);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'dateTimeRange',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ],
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['dateTimeRange'], 'default', 'value' => $this->defaultDTStart . ' - ' . $this->defaultDTEnd],
            [['dateTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['createTimeStart', 'createTimeEnd'], 'safe'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'uf_title' => 'Title',
            'uf_message' => 'Message',
            'dateTimeRange' => 'Date Time Range',
        ];
    }

    public function formName()
    {
        return 'UserActivitySearch';
    }
}
