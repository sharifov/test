<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\AbacPolicy */
?>
<div class="abac-policy-view">
    <h2>Policy Id: <?= Html::encode($model->ap_id) ?> - <?= Html::encode($model->ap_object) ?> - <?= Html::encode($model->ap_action) ?></h2>
    <?php echo Html::textarea('dump', $model->getDump(), ['rows' => 11, 'style' => 'width: 100%']) ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            HashCode: <?= Html::encode($model->ap_hash_code) ?>
        </div>
    </div>
</div>