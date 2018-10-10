<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserGroupAssign */

$this->title = ($model->ugsUser ? $model->ugsUser->username : '').' / '.($model->ugsGroup ? $model->ugsGroup->ug_name : '');
$this->params['breadcrumbs'][] = ['label' => 'User Group Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-group-assign-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ugs_user_id' => $model->ugs_user_id, 'ugs_group_id' => $model->ugs_group_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ugs_user_id' => $model->ugs_user_id, 'ugs_group_id' => $model->ugs_group_id], [
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

            [
                'attribute' => 'ugs_user_id',
                'value' => function(\common\models\UserGroupAssign $model) {
                    return $model->ugsUser ? $model->ugsUser->username : '-' ;
                },
            ],

            [
                'attribute' => 'ugs_group_id',
                'value' => function(\common\models\UserGroupAssign $model) {
                    return $model->ugsGroup ? $model->ugsGroup->ug_name : '-' ;
                },
            ],

            //'ugs_updated_dt',
            [
                'attribute' => 'ugs_updated_dt',
                'value' => function(\common\models\UserGroupAssign $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->ugs_updated_dt));
                },
                'format' => 'html',
            ],
        ],
    ]) ?>

</div>
