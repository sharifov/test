<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\abacDoc\AbacDoc */

$this->title = $model->ad_id;
$this->params['breadcrumbs'][] = ['label' => 'Abac Docs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="abac-doc-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ad_id',
                'ad_file',
                'ad_line',
                'ad_subject',
                'ad_object',
                'ad_action',
                'ad_description',
                'ad_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
