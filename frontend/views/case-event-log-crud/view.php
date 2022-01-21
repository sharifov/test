<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\VarDumper;
use src\entities\cases\CaseEventLog;

/* @var $this yii\web\View */
/* @var $model src\entities\cases\CaseEventLog */

$this->title = $model->cel_id;
$this->params['breadcrumbs'][] = ['label' => 'Case Event Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="case-event-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cel_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cel_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'cel_id',
            'cel_case_id',
            [
                'attribute' => 'cel_type_id',
                'value' => static function (CaseEventLog $model) {
                    return $model->cel_type_id ? CaseEventLog::CASE_EVENT_LOG_LIST[$model->cel_type_id] : null;
                }
            ],
            [
                'attribute' => 'cel_category_id',
                'value' => static function (CaseEventLog $model) {
                    return $model->getCategoryNameFormat();
                },
                'format' => 'raw'
            ],
            'cel_description',
            'cel_created_dt',
        ],
    ]) ?>
    </div>
    <div class="col-md-6">
        <strong><?php echo $model->getAttributeLabel('cel_data_json') ?></strong><br />
        <pre><small><?php VarDumper::dump($model->cel_data_json, 20, true); ?></small></pre><br />
    </div>

</div>
