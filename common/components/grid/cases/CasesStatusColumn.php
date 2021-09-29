<?php

namespace common\components\grid\cases;

use Yii;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;

class CasesStatusColumn extends DataColumn
{
    public $format = 'raw';
    public $logRecord;

    public function init(): void
    {
        if ($this->attribute === null) {
            $this->attribute = 'cs_status';
        }

        if ($this->filter === null) {
            $this->filter = CasesStatus::STATUS_LIST;
        }

        if ($this->logRecord === null) {
            $this->logRecord = true;
        }

        $this->contentOptions = ArrayHelper::merge($this->contentOptions, ['class' => 'text-center']);

        parent::init();
    }

    protected function renderDataCellContent($model, $key, $index): string
    {
        /** @var Cases $model */
        $value = CasesStatus::getName($model->cs_status);
        $str = '<span class="label ' . CasesStatus::getClass($model->cs_status) . '">' . $value . '</span>';
        if ($model->lastLogRecord && $this->logRecord) {
            $str .= '<br><br><span class="label label-default">' . Yii::$app->formatter->asDatetime(strtotime($model->lastLogRecord->csl_start_dt)) . '</span>';
            $str .= '<br>';
            $str .= $model->lastLogRecord ? Yii::$app->formatter->asRelativeTime(strtotime($model->lastLogRecord->csl_start_dt)) : '';
        }
        return $str;
    }
}
