<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTaskScenario */

$this->title = $model->ots_id;
$this->params['breadcrumbs'][] = ['label' => 'Object Task Scenarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

if (!empty($model->ots_data_json)) {
    $model->ots_data_json = Json::encode($model->ots_data_json);
}
?>
<div class="object-task-scenario-view col-6">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ots_id' => $model->ots_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ots_id' => $model->ots_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ots_id',
            'ots_enable:booleanByLabel',
            'ots_key',
            'ots_data_json',
            'ots_updated_dt',
            'ots_updated_user_id',
        ],
    ]) ?>

</div>
