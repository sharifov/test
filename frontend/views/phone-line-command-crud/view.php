<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\PhoneLineCommand */

$this->title = $model->plc_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Line Commands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="phone-line-command-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->plc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->plc_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'plc_id',
                    'plc_line_id',
                    'plc_ccom_id',
                    'plc_sort_order',
                    'plc_created_user_id:userName',
                    'plc_created_dt:byUserDateTime',
                ],
            ]) ?>
        </div>
    </div>

</div>
