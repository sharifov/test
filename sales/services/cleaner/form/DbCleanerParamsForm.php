<?php

namespace sales\services\cleaner\form;

use sales\services\cleaner\DbCleanerService;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class DbCleanerParamsForm
 * @property string|null $table
 * @property string|null $column
 * @property int|null $hour
 * @property int|null $day
 * @property int|null $month
 * @property int|null $year
 * @property string|null $date
 * @property string|null $datetime
 * @property string|null $strict_date
 */
class DbCleanerParamsForm extends Model
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

    private $brokenParams = [];

    public function rules(): array
    {
        return [
            [['table', 'column'], 'required'],

            ['table', 'tableColumnValidate'],
            ['table', 'paramsValidate'],

            [['hour', 'day', 'month', 'year'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['hour', 'integer', 'min' => 1, 'max' => 24],
            ['day', 'integer', 'min' => 1, 'max' => 31],
            ['month', 'integer', 'min' => 1, 'max' => 12],
            ['year', 'integer', 'min' => 1, 'max' => 10],

            [['table', 'column'], 'string', 'max' => 50],

            [['date', 'strict_date'], 'datetime', 'format' => 'php:Y-m-d'],

            [['datetime'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function fillParam(?array $params): DbCleanerParamsForm
    {
        foreach ($params as $key => $value) {
            if (ArrayHelper::isIn($key, DbCleanerService::ALLOWED_PARAMS)) {
                $this->{$key} = $value;
            } else {
                $this->brokenParams[] = $key;
            }
        }
        return $this;
    }

    /**
     * @param $attribute
     */
    public function tableColumnValidate($attribute): void
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema($this->table);
        if ($tableSchema === null) {
            $this->addError($attribute, 'Table (' . $this->table . ') is not exist in DB');
        } elseif (!$tableSchema->getColumn($this->column)) {
            $this->addError($attribute, 'Column (' . $this->column . ') is not exist in table (' . $this->table . ')');
        }
    }

    /**
     * @param $attribute
     */
    public function paramsValidate($attribute): void
    {
        $result = false;
        foreach (DbCleanerService::ALLOWED_PARAMS as $param) {
            if (!empty($this->{$param})) {
                $result = true;
                break;
            }
        }
        if (!$result) {
            $this->addError($attribute, 'At least one parameter (' .
                implode(',', DbCleanerService::ALLOWED_PARAMS) . ') must be filled');
        }
    }

    public function setTable(string $table): DbCleanerParamsForm
    {
        $this->table = $table;
        return $this;
    }

    public function setColumn(string $column): DbCleanerParamsForm
    {
        $this->column = $column;
        return $this;
    }

    public function formName(): string
    {
        return '';
    }
}
