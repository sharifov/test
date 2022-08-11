<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;
use frontend\models\CommunicationForm;
use common\models\QuoteCommunication;

/**
 * @var $this yii\web\View
 * @var $model \common\models\QuoteCommunication
 **/

$this->title = $model->qcol_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Communication Open Log', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-communication-open-log-view">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <p>
        <?= Html::a('Update', ['update', 'qcol_id' => $model->qcol_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'qcol_id' => $model->qcol_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php
    try {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'qcol_id',
                'qcol_quote_communication_id',
                'qcol_created_dt:byUserDateTime',
            ],
        ]);
    } catch (\Exception $e) {
        echo Html::tag('pre', Html::encode($e->getMessage()));
    }
    ?>
</div>
