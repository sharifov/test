<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLineUserGroup\entity\PhoneLineUserGroup */

$this->title = $model->plug_line_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Line User Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="phone-line-user-group-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'plug_line_id' => $model->plug_line_id, 'plug_ug_id' => $model->plug_ug_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'plug_line_id' => $model->plug_line_id, 'plug_ug_id' => $model->plug_ug_id], [
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
                'plug_line_id',
                'plug_ug_id',
                'plug_created_dt:byUserDateTime',
                'plug_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
