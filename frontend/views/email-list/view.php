<?php

use sales\model\emailList\entity\EmailList;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model EmailList */

$this->title = $model->el_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="email-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->el_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->el_id], [
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
            'el_id',
            'el_email:email',
            'el_title',
            'el_enabled:booleanByLabel',
            'el_created_user_id:userName',
            'el_updated_user_id:userName',
            'el_created_dt:byUserDateTime',
            'el_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
