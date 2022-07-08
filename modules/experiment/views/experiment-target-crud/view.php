<?php

use modules\experiment\models\Experiment;
use modules\experiment\models\ExperimentTarget;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\experiment\models\Experiment */
/* @var $model modules\experiment\models\ExperimentTarget */

$this->title = $model->ext_id;
$this->params['breadcrumbs'][] = ['label' => 'Experiment Targets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="experiment-target-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ext_id' => $model->ext_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ext_id' => $model->ext_id], [
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
            'ext_target_id',
            [
                'attribute' => 'ext_target_type_id',
                'value' => static function (ExperimentTarget $model) {
                    return ExperimentTarget::EXT_TYPE_LIST[$model->ext_target_type_id];
                }
            ],
            [
                'attribute' => 'ext_experiment_id',
                'value' => static function (ExperimentTarget $model) {
                    return Experiment::getExperimentById($model->ext_experiment_id)['ex_code'] . ' (ID ' . $model->ext_experiment_id . ')';
                }
            ],
            'ext_created_dt'
        ],
    ]) ?>

</div>
