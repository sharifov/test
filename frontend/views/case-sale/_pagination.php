<?php

/* @var $model common\models\search\CaseSaleSearch */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<nav>
    <ul class="pagination">
        <li class="page-item first <?= $model->reset ? null : 'disabled' ?>">
            <?= Html::a('Reset', [Url::toRoute(['case-sale/index'])], ['class' => 'page-link']) ?>
        </li>
        <li class="page-item prev <?= (bool)$model->prevId ? null : 'disabled' ?>">
            <?= Html::a('Previous', [Url::current([
                'CaseSaleSearch[prevId]' => $model->prevId,
                'CaseSaleSearch[cursor]' => 1
            ])], ['class' => 'page-link']) ?>
        </li>
        <li class="page-item next <?= (bool) $model->nextId ? null : 'disabled' ?>">
            <?= Html::a('Next', [Url::current([
                'CaseSaleSearch[nextId]' => $model->nextId,
                'CaseSaleSearch[cursor]' => 2
            ])], ['class' => 'page-link']) ?>
        </li>
    </ul>
</nav>
