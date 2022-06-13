<?php

use common\components\experimentManager\models\ExperimentTarget;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\components\experimentManager\models\ExperimentTarget */

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
                'attribute' => 'ext_target_type',
                'value' => static function (ExperimentTarget $model) {
                    return ExperimentTarget::EXT_TYPE_LIST[$model->ext_target_type];
                }
            ],
            'ext_experiment_id',
        ],
    ]) ?>

</div>
