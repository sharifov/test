<?php

namespace sales\services\cleaner\form;

use sales\services\cleaner\DbCleanerService;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class LogCleanerForm
 * @property string|null $table
 * @property string|null $column
 * @property int|null $hour
 * @property int|null $day
 * @property int|null $month
 * @property int|null $year
 * @property string|null $date
 * @property string|null $datetime
 * @property string|null $strict_date
 * @property string|null $category
 * @property string|null $level
 */
class LogCleanerForm extends DbCleanerParamsForm
{
    public $table;
    public $column;
    public $hour;
    public $day;
    public $month;
    public $year;
    public $date;
    public $datetime;
    public $strict_date;
    public $category;
    public $level;

    private $brokenParams = [];

    public function rules(): array
    {
        $rules = [
            ['level', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['level', 'integer'],

            ['category', 'string', 'max' => 100],
        ];
        return ArrayHelper::merge(parent::rules(), $rules);
    }
}
