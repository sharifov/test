<?php

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\UserContactList;
use common\models\UserProfile;
use sales\access\CallAccess;
use sales\access\ContactUpdateAccess;
use sales\auth\Auth;
use sales\helpers\call\CallHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var yii\web\View $this */
/* @var common\models\Client $model */

$this->title = 'Contact: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Contact', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ((new ContactUpdateAccess())->isUserCanUpdateContact($model, Auth::user())): ?>
            <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif ?>
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
            'is_company:booleanByLabel',
            'is_public:booleanByLabel',
            'disabled:booleanByLabel',
            [
                'attribute' => 'ucl_favorite',
                'value' => static function(Client $model) {
                    $out = '<span class="not-set">(not set)</span>';
                    if ($model->contact) {
                        $out = $model->contact->ucl_favorite ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
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
                        'value' => static function(Client $model) {
                            $phones = $model->clientPhones;
                            $data = [];
                            if($phones) {
                                foreach ($phones as $k => $phone) {
                                    $title = $phone->cp_title ? ' <em>(' . $phone->cp_title . ')</em>' : '' ;
                                    $data[] =
                                        CallHelper::callNumber($phone->phone, CallAccess::isUserCanDial(Auth::id(),
                                        UserProfile::CALL_TYPE_WEB), '',  ['disable-icon' => true], 'code') .
                                        $title;
                                }
                            }

                            return implode('<br>', $data);
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                    ],
                    [
                        'label' => 'Emails',
                        'value' => static function(Client $model) {
                            $emails = $model->clientEmails;
                            $data = [];
                            if($emails) {
                                foreach ($emails as $k => $email) {
                                    $title = $email->ce_title ? ' <em>(' . $email->ce_title . ')</em>' : '' ;
                                    $data[] = ' <code>' . Html::encode($email->email) . '</code>' . $title ;
                                }
                            }

                            return implode('<br>', $data);
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                    ],
                    /*[
                        'label' => 'Projects',
                        'value' => static function (Client $model) {
                            $str = '';
                            foreach ($model->projects as $project) {
                                $str .= '<div style="margin: 1px;">' . Yii::$app->formatter->asProjectName($project->name) . '</div>';
                            }
                            return $str;
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                    ],*/
                    [
                        'attribute' => 'created',
                        'value' => function(Client $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'updated',
                        'value' => function(Client $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                        },
                        'format' => 'html',
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
