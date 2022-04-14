<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign */

$this->title = $model->usa_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Shift Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-shift-assign-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'usa_user_id' => $model->usa_user_id, 'usa_sh_id' => $model->usa_sh_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'usa_user_id' => $model->usa_user_id, 'usa_sh_id' => $model->usa_sh_id], [
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
                'usa_user_id:username',
                'usa_sh_id',
                'usa_created_dt:byUserDateTime',
                'usa_created_user_id:username',
            ],
        ]) ?>

    </div>

</div>
