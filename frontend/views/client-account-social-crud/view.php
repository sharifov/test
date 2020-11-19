<?php

use sales\model\clientAccountSocial\entity\ClientAccountSocial;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var yii\web\View $this */
/* @var sales\model\clientAccountSocial\entity\ClientAccountSocial $model */

$this->title = $model->cas_ca_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Account Socials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-account-social-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'cas_ca_id' => $model->cas_ca_id, 'cas_type_id' => $model->cas_type_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'cas_ca_id' => $model->cas_ca_id, 'cas_type_id' => $model->cas_type_id], [
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
                'cas_ca_id',
                [
                    'attribute' => 'cas_type_id',
                    'value' => static function (ClientAccountSocial $model) {
                        if ($model->cas_type_id === ClientAccountSocial::TYPE_GOOGLE) {
                            return '<i class="fa fa-google"></i>';
                        }
                        if ($model->cas_type_id === ClientAccountSocial::TYPE_FACEBOOK) {
                            return '<i class="fa fa-facebook"></i>';
                        }
                        return '---';
                    },
                    'format' => 'raw',
                ],
                'cas_identity',
                'cas_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
