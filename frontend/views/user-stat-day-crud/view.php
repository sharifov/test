<?php

use sales\model\userStatDay\entity\UserStatDay;
use sales\model\userStatDay\entity\UserStatDayKey;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\userStatDay\entity\UserStatDay */

$this->title = $model->usd_id;
$this->params['breadcrumbs'][] = ['label' => 'User Stat Days', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-stat-day-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'usd_id' => $model->usd_id, 'usd_month' => $model->usd_month, 'usd_year' => $model->usd_year], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'usd_id' => $model->usd_id, 'usd_month' => $model->usd_month, 'usd_year' => $model->usd_year], [
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
            'usd_id',
            [
                'attribute' => 'usd_key',
                'value' => static function (UserStatDay $model) {
                    return UserStatDayKey::getNameById($model->usd_key);
                },
                'filter' => UserStatDayKey::getList()
            ],
            'usd_value',
            'usd_user_id:userName',
            'usd_day',
            'usd_month',
            'usd_year',
            'usd_created_dt:byUserDateTime',
        ],
    ]) ?>

</div>
