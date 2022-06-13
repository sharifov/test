<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\components\experimentManager\models\Experiment */

$this->title = $model->ex_id;
$this->params['breadcrumbs'][] = ['label' => 'Experiments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="experiment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ex_id' => $model->ex_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ex_id' => $model->ex_id], [
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
            'ex_id',
            'ex_code',
        ],
    ]) ?>

</div>
