<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLineUserAssign\entity\PhoneLineUserAssign */

$this->title = $model->plus_line_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Line User Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="phone-line-user-assign-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'plus_line_id' => $model->plus_line_id, 'plus_user_id' => $model->plus_user_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'plus_line_id' => $model->plus_line_id, 'plus_user_id' => $model->plus_user_id], [
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
                'plus_line_id',
                'plus_user_id:username',
                'plus_allow_in:BooleanByLabel',
                'plus_allow_out:BooleanByLabel',
                'plus_uvm_id',
                'plus_enabled:BooleanByLabel',
                'plus_settings_json:dumpJson',
                'plus_created_user_id:username',
                'plus_updated_user_id:username',
                'plus_created_dt:byUserDateTime',
                'plus_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
