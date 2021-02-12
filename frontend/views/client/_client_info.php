<?php

use common\models\Client;
use common\models\Employee;
use sales\logger\formatter\ClientFormatter;
use yii\helpers\Html;
use yii\widgets\DetailView;
use sales\helpers\email\MaskEmailHelper;
use sales\helpers\phone\MaskPhoneHelper;

/** @var $model Client */

/** @var Employee $user */
$user = Yii::$app->user->identity;
$unsubscribedEmails = array_column($model->project->emailUnsubscribes, 'eu_email');
?>
<div class="row">
    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'attribute' => 'first_name',
                    'value' => static function (Client $client) {
                        return \sales\model\client\helpers\ClientFormatter::formatName($client);
                    },
                    'format' => 'raw',
                ],
                'middle_name',
                'last_name',
                'locale',
            ],
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'project:projectName',
                [
                    'label' => 'Phones',
                    'value' => static function (Client $model) {
                        $data = [];
                        foreach ($model->clientPhones as $k => $phone) {
                            $data[] = '<i class="fa fa-phone"></i> <code>' . Html::encode(MaskPhoneHelper::masking($phone->phone)) . '</code> ' . $phone::getPhoneTypeLabel($phone->type);
                        }
                        return implode('<br>', $data);
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-left'],
                ],
                [
                    'label' => 'Emails',
                    'value' => static function (Client $model) use ($unsubscribedEmails) {
                        $data = [];
                        foreach ($model->clientEmails as $k => $email) {
                            $unsubscribedIcon = in_array($email->email, $unsubscribedEmails) ? ' <i title="Unsubscribed" class="fa fa-bell-slash"></i>' : '';
                            $data[] = '<i class="fa fa-envelope"></i> <code>' . Html::encode(MaskEmailHelper::masking($email->email)) . '</code> ' . $email::getEmailTypeLabel($email->type) . $unsubscribedIcon;
                        }
                        return implode('<br>', $data);
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-left'],
                ],
                [
                    'attribute' => 'created',
                    'value' => static function (Client $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                    },
                    'format' => 'html',
                    'visible' => !$user->isAgent(),
                ],
                [
                    'attribute' => 'updated',
                    'value' => static function (Client $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                    },
                    'format' => 'html',
                    'visible' => !$user->isAgent(),
                ],
            ],
        ]) ?>
    </div>
</div>
