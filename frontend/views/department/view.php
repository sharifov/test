<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Department */

$this->title = $model->dep_name;
$this->params['breadcrumbs'][] = ['label' => 'Departments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="department-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->dep_id], ['class' => 'btn btn-primary']) ?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->dep_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'dep_id',
            'dep_key',
            'dep_name',
            //'dep_updated_user_id',
            //'dep_updated_dt',
            [
                'attribute' => 'dep_updated_user_id',
                'value' => function (\common\models\Department $model) {
                    return $model->dep_updated_user_id ? '<i class="fa fa-user"></i> ' .Html::encode($model->depUpdatedUser->username) : $model->dep_updated_user_id;
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'dep_updated_dt',
                'value' => function (\common\models\Department $model) {
                    return $model->dep_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->dep_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],
        ],
    ]) ?>

</div>
