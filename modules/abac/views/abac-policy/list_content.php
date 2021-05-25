<?php

use modules\abac\src\entities\AbacPolicy;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $policyListContent string */

$this->title = 'ABAC policy list content';
$this->params['breadcrumbs'][] = ['label' => 'Abac Policies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="abac-policy-list-content">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><i class="fa fa-info-circle"></i> from Cache for Casbin</p>
    <p>
        <?= Html::a('<i class="fa fa-remove"></i> Reset Cache', ['invalidate-cache'], ['class' => 'btn btn-warning']) ?>
    </p>
    <pre><?php
            echo Html::encode($policyListContent);
    ?>
    </pre>


</div>
