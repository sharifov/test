<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Log */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-view">

    <p>
        <?= Html::a('<i class="fas fa-remove"></i> Delete', ['delete', 'id' => $model->id], [
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
            'id',
            'level',
            'category',
            'log_time:datetime',
            'prefix:ntext',
            //'message:ntext',
            [
                'attribute' => 'message',
                'format' => 'raw',
                'value' => static function(\frontend\models\Log $model) {
                    return $model->message ? '<pre>'.$model->message.'</pre>' : '-';
                },
            ],
        ],
    ]) ?>

</div>
