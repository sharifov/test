<?php

use sales\helpers\text\SecureStringHelper;
use sales\model\userClientChatData\entity\UserClientChatData;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\userClientChatData\entity\UserClientChatData */

$this->title = $model->uccd_id;
$this->params['breadcrumbs'][] = ['label' => 'User Client Chat Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-client-chat-data-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">
        <p>
            <?= Html::a('Update', ['update', 'id' => $model->uccd_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->uccd_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Warning. Item will be hard deleted without synchronization with RC server.',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'uccd_id',
                'uccd_rc_user_id',
                'uccd_username',
                'uccd_name',
                [
                    'attribute' => 'uccd_password',
                    'value' => static function (UserClientChatData $model) {
                        return SecureStringHelper::generate((string) $model->uccd_password);
                    },
                    'format' => 'raw',
                ],
                'uccd_auth_token',
                'uccd_employee_id:username',
                'uccd_token_expired',
                'uccd_active:booleanByLabel',
                'uccd_created_dt:byUserDateTime',
                'uccd_updated_dt:byUserDateTime',
                'uccd_created_user_id:username',
                'uccd_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
