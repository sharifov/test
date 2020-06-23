<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\callNote\entity\CallNote */

$this->title = $model->cn_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-note-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cn_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cn_id], [
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
                'cn_id',
                'cn_call_id:callLog',
                'cn_note',
                'cn_created_dt:byUserDateTime',
                'cn_updated_dt:byUserDateTime',
                'cn_created_user_id:username',
                'cn_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
