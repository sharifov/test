<?php

use common\models\Client;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Client */

$this->title = 'Client: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php /*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <div class="row">
    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'uuid',
            'first_name',
            'middle_name',
            'last_name',
            'company_name',
            'description',
            'is_company:boolean',
            'is_public:boolean',
            'disabled:boolean',
            'rating',
            [
                'attribute' => 'parent_id',
                'value' => function(\common\models\Client $model) {

                    $out = '<span class="not-set">(not set)</span>';
                    if ($model->parent_id && $parent = Client::findOne(['id' => $model->parent_id])) {
                        return '<i class="fa fa-user"></i> ' . $parent->full_name;
                    }
                    return $out;
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>
    </div>
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Phones',
                        'value' => function(\common\models\Client $model) {

                            $phones = $model->clientPhones;
                            $data = [];
                            if($phones) {
                                foreach ($phones as $k => $phone) {
                                    $data[] = '<i class="fa fa-phone"></i> <code>'.Html::encode($phone->phone).'</code>'; //<code>'.Html::a($phone->phone, ['client-phone/view', 'id' => $phone->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                                }
                            }

                            $str = implode('<br>', $data);
                            return ''.$str.'';
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                    ],

                    [
                        'label' => 'Emails',
                        'value' => function(\common\models\Client $model) {

                            $emails = $model->clientEmails;
                            $data = [];
                            if($emails) {
                                foreach ($emails as $k => $email) {
                                    $data[] = '<i class="fa fa-envelope"></i> <code>'.Html::encode($email->email).'</code>';
                                }
                            }

                            $str = implode('<br>', $data);
                            return ''.$str.'';
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                    ],

                    //'created',
                    //'updated',

                    [
                        'attribute' => 'created',
                        'value' => function(\common\models\Client $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'updated',
                        'value' => function(\common\models\Client $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                        },
                        'format' => 'html',
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
