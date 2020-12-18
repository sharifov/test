<?php

use common\models\Client;
use common\models\Employee;
use sales\logger\formatter\ClientFormatter;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var $model Client */

/** @var Employee $user */
$user = Yii::$app->user->identity;

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
                            $data[] = '<i class="fa fa-phone"></i> <code>' . Html::encode($phone->phone) . '</code> ' . $phone::getPhoneTypeLabel($phone->type);
                        }
                        return implode('<br>', $data);
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-left'],
                ],
                [
                    'label' => 'Emails',
                    'value' => static function (Client $model) {
                        $data = [];
                        foreach ($model->clientEmails as $k => $email) {
                            $data[] = '<i class="fa fa-envelope"></i> <code>' . Html::encode($email->email) . '</code> ' . $email::getEmailTypeLabel($email->type);
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
