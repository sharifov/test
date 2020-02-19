<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskRules\QaTaskRules */

$this->title = $model->tr_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="qa-task-rules-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->tr_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'tr_id',
            'tr_key',
            'tr_type:qaTaskObjectType',
            'tr_name',
            'tr_description',
            'tr_parameters:ntext',
            'tr_enabled:booleanByLabel',
            'createdUser:userName',
            'updatedUser:userName',
            'tr_created_dt:byUserDateTime',
            'tr_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
