<?php

use modules\email\src\entity\emailAccount\EmailAccount;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\email\src\entity\emailAccount\EmailAccount */

$this->title = $model->ea_id;
$this->params['breadcrumbs'][] = ['label' => 'Email Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="email-account-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ea_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ea_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Request Gmail API access token', ['email-account/request-access-token', 'id' => $model->ea_id], ['class' => 'btn btn-success']) ?>
            <?php if ($model->ea_gmail_token): ?>
                <?= Html::a('Remove Gmail API access token', ['email-account/remove-access-token', 'id' => $model->ea_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete Gmail API access token?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ea_id',
                'ea_email:email',
                'ea_imap_settings:ntext',
                [
                    'attribute' => 'ea_protocol',
                    'value' => static function (EmailAccount $model) {
                        return EmailAccount::PROTOCOL_LIST[$model->ea_protocol];
                    }
                ],
                [
                    'attribute' => 'ea_gmail_command',
                    'value' => static function (EmailAccount $model) {
                        return EmailAccount::GMAIL_COMMAND_LIST[$model->ea_gmail_command];
                    }
                ],
                'ea_gmail_token:ntext',
//                'ea_options:ntext',
                'ea_active:booleanByLabel',
                'createdUser:userName',
                'updatedUser:userName',
                'ea_created_dt:byUserDateTime',
                'ea_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
