<?php

use sales\yii\i18n\Formatter;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PhoneBlacklist */

$this->title = $model->pbl_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Blacklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="phone-blacklist-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pbl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pbl_id], [
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
            'pbl_id',
            'pbl_phone',
            'pbl_description',
            'pbl_enabled:booleanByLabel',
            'pbl_created_dt:byUserDateTime',
            'pbl_updated_dt:byUserDateTime',
            'updatedUser:userName',
        ],
    ]) ?>

</div>
