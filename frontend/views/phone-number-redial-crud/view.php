<?php

use src\model\phoneNumberRedial\entity\PhoneNumberRedial;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\phoneNumberRedial\entity\PhoneNumberRedial */

$this->title = $model->pnr_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Number Redials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="phone-number-redial-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('go back', ['index'], ['class' => 'btn btn-default']) ?>
            <?= Html::a('Update', ['update', 'pnr_id' => $model->pnr_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'pnr_id' => $model->pnr_id], [
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
                'pnr_id',
                'pnr_project_id:projectName',
                'pnr_phone_pattern',
                [
                    'attribute' => 'pnr_pl_id',
                    'value' => static function (PhoneNumberRedial $model): string {
                        return Html::encode($model->phoneList->pl_phone_number);
                    }
                ],
                'pnr_name',
                'pnr_enabled:booleanByLabel',
                'pnr_priority',
                'pnr_created_dt:byUserDateTime',
                'pnr_updated_dt:byUserDateTime',
                'pnr_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
