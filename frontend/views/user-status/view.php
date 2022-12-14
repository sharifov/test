<?php

use common\components\grid\DateTimeColumn;
use src\model\user\entity\userStatus\UserStatus;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\user\entity\userStatus\UserStatus */

$this->title = $model->us_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-status-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->us_user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->us_user_id], [
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
            'us_user_id',
            'us_user_id:UserName',
            'us_gl_call_count',
            'us_call_phone_status:boolean',
            'us_is_on_call:boolean',
            'us_has_call_access:boolean',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'us_phone_ready_time',
                'value' => static function (UserStatus $model) {
                    return $model->us_phone_ready_time ? date('Y-m-d H:i:s', $model->us_phone_ready_time) : null;
                },
                'format' => 'byUserDateTimeWithSeconds',
            ],
            'us_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
