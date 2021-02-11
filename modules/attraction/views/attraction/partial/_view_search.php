<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

?>

<div class="attraction-view-search">
    <div class="row">
        <div class="col-md-12">
            <h5 title="atn_id: <?= $model->atn_id?>"> Destination:  (<?=Html::encode($model->atn_destination_code)?>)  <?=Html::encode($model->atn_destination)?></h5>
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'atn_date_from:date',
                        'atn_date_to:date',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
