<?php

use common\models\Client;
use common\models\Employee;
use src\logger\formatter\ClientFormatter;
use yii\helpers\Html;
use yii\widgets\DetailView;
use src\helpers\email\MaskEmailHelper;
use src\helpers\phone\MaskPhoneHelper;
use modules\cases\src\abac\CasesAbacObject;
use modules\cases\src\abac\dto\CasesAbacDto;

/**
 * @var Employee $user
 * @var $model Client
 * @var $case \src\entities\cases\Cases
 * @var $disableMasking bool
 */

$user = Yii::$app->user->identity;
$unsubscribedEmails = [];
if ($model->project) {
    $unsubscribedEmails = array_column($model->project->emailUnsubscribes, 'eu_email');
}

if ($case) {
    /** @abac new CasesAbacDto($case), CasesAbacObject::LOGIC_CLIENT_DATA, CasesAbacObject::ACTION_UNMASK, Disable mask client data on Case details popup */
    $disableMasking = Yii::$app->abac->can(new CasesAbacDto($case), CasesAbacObject::LOGIC_CLIENT_DATA, CasesAbacObject::ACTION_UNMASK);
} else {
    $disableMasking = false;
}

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
                        return \src\model\client\helpers\ClientFormatter::formatName($client);
                    },
                    'format' => 'raw',
                ],
                'middle_name',
                'last_name',
                'cl_locale',
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
                    'value' => static function (Client $model) use ($disableMasking) {
                        $data = [];
                        foreach ($model->clientPhones as $k => $phone) {
                            $data[] = '<i class="fa fa-phone"></i> <code>' . Html::encode(MaskPhoneHelper::masking($phone->phone, $disableMasking)) . '</code> ' . $phone::getPhoneTypeLabel($phone->type);
                        }
                        return implode('<br>', $data);
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-left'],
                ],
                [
                    'label' => 'Emails',
                    'value' => static function (Client $model) use ($unsubscribedEmails, $disableMasking) {
                        $data = [];
                        foreach ($model->clientEmails as $k => $email) {
                            $unsubscribedIcon = in_array($email->email, $unsubscribedEmails) ? ' <i title="Unsubscribed" class="fa fa-bell-slash"></i>' : '';
                            $data[] = '<i class="fa fa-envelope"></i> <code>' . Html::encode(MaskEmailHelper::masking($email->email, $disableMasking)) . '</code> ' . $email::getEmailTypeLabel($email->type) . $unsubscribedIcon;
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
