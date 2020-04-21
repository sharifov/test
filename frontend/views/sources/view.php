<?php

use common\models\Sources;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Sources */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Sources', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sources-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'id',
            [
                'attribute' => 'rule',
                'value' => static function (Sources $source) {
                    return Sources::LIST_RULES[$source->rule] ?? 'Undefined';
                },
            ],
            'default:boolean',
            'hidden:boolean',
            'project_id:projectName',
            'name',
            'cid',
            //'last_update',
            [
                'attribute' => 'last_update',
                'value' => static function (\common\models\Sources $model) {
                    return $model->last_update ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->last_update)) : '-';
                },
                'format' => 'raw'
            ],

        ],
    ]) ?>

</div>
