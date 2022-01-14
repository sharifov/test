<?php

use src\model\contactPhoneData\service\ContactPhoneDataHelper;
use src\model\contactPhoneList\entity\ContactPhoneList;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\contactPhoneList\entity\ContactPhoneList */

$this->title = $model->cpl_id;
$this->params['breadcrumbs'][] = ['label' => 'Contact Phone Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="contact-phone-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'cpl_id',
                'cpl_phone_number',
                'cpl_uid',
                'cpl_title',
                [
                    'label' => 'Phone Data',
                    'value' => static function (ContactPhoneList $model) {
                        $result = '';
                        if ($model->contactPhoneData) {
                            foreach ($model->contactPhoneData as $contactPhoneData) {
                                $result .= '<p style="margin-bottom: 6px;">' .
                                    ContactPhoneDataHelper::getLabel($contactPhoneData->cpd_key) . ' : ' .
                                    ContactPhoneDataHelper::getLabelValue($contactPhoneData->cpd_key, $contactPhoneData->cpd_value) . '</p>';
                            }
                        }
                        return $result;
                    },
                    'format' => 'raw',
                ],
                'cpl_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
