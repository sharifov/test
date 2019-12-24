<?php

use common\models\UserGroup;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserGroup */

$this->title = $model->ug_name;
$this->params['breadcrumbs'][] = ['label' => 'User Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-group-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ug_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ug_id], [
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
            'ug_id',
            'ug_key',
            'ug_name',
            'ug_description',
            'ug_processing_fee',
            'ug_disable:booleanByLabel',
            'ug_on_leaderboard:booleanByLabel',
            [
                'attribute' => 'ug_user_group_set_id',
                'value' => static function (UserGroup $model) {
                    if ($model->ug_user_group_set_id) {
                        return $model->userGroupSet->ugs_name;
                    }
                    return '';
                },
            ],
            'ug_updated_dt:dateTimeByUserDt',
        ],
    ]) ?>

</div>
