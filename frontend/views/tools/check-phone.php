<?php

use frontend\helpers\JsonHelper;
use sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var string $phone */
/** @var array $errors */
/** @var array $dbResult */
/** @var array $apiResult */
/** @var bool $checkTwilio */
/** @var bool $checkNeutrino */

$this->title = 'Check Phone';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="check-phone">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(['id' => 'pjax-check-phone', 'timeout' => 9000]); ?>
    <div class="row">
        <div class="col-md-2">
            <?php if ($errors) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ($errors as $value) : ?>
                        <p><?php echo $value ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <?= Html::beginForm(null, 'post', [
                'data-pjax' => 1
            ]) ?>

            <div class="form-group">
                <?= Html::label('Phone number:') ?>
                <?= Html::input('text', 'phone', $phone, ['minlength' => 8, 'maxlength' => 50, 'style' => 'width: 100%;']) ?>
            </div>
            <div class="form-group">
                <?= Html::label('Services:') ?><br />
                <?= Html::label('Twilio') ?>&nbsp;
                <?= Html::checkbox('check_twilio', $checkTwilio) ?>&nbsp;&nbsp;&nbsp;
                <?= Html::label('Neutrino') ?>&nbsp;
                <?= Html::checkbox('check_neutrino', $checkNeutrino) ?>
            </div>

            <?= Html::submitButton('Check Phone', ['class' => 'btn btn-primary']) ?>
            <?= Html::endForm() ?>
        </div>

        <div class="col-md-5">
            <?php if ($dbResult) : ?>
                <p>Db Result (PhoneServiceInfo):</p>
                <?php foreach ($dbResult as $service => $value) : ?>
                    <h6><?php echo ucfirst($service) ?></h6>
                    <?php $dataJson = JsonHelper::decode($value['cpsi_data_json']) ?>
                    <pre><small><?php VarDumper::dump($dataJson, 20, true); ?></small></pre>
                <?php endforeach ?>
            <?php endif ?>
        </div>

         <div class="col-md-5">
            <?php if ($apiResult) : ?>
                <p>Api Neutrino Result:</p>
                <?php foreach ($apiResult as $service => $value) : ?>
                <h6><?php echo ucfirst($service) ?></h6>
                <pre><small><?php VarDumper::dump($value, 20, true); ?></small></pre>
                <?php endforeach ?>
            <?php endif ?>
         </div>
    </div>
    <?php Pjax::end(); ?>
</div>
