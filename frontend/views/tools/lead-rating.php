<?php

use modules\smartLeadDistribution\src\SmartLeadDistribution;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var integer $id */
/** @var array{points: integer, log: \modules\smartLeadDistribution\src\entities\LeadRatingProcessingLog[]} $dataRating */
/** @var array $errors */

$this->title = 'Lead Rating';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="check-phone">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(['id' => 'pjax-lead-rating', 'timeout' => 9000]); ?>
    <div class="row">
        <div class="col-md-2">
            <?php if ($errors) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ($errors as $value) : ?>
                        <p><?php echo $value ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <?= Html::beginForm('/tools/lead-rating', 'get', [
                'data-pjax' => 1
            ]) ?>

            <div class="form-group">
                <?= Html::label('Lead ID/UID:') ?>
                <?= Html::input('text', 'id', $id, ['minlength' => 1, 'style' => 'width: 100%;']) ?>
            </div>

            <?= Html::submitButton('Count lead rating', ['class' => 'btn btn-primary']) ?>
            <?= Html::endForm() ?>
        </div>

        <div class="col-md-5">
            <?php if (!empty($dataRating)) : ?>
                <h3>Lead category: <?= SmartLeadDistribution::CATEGORY_LIST[$dataRating['category']] ?></h3>

                <?php if (!empty($dataRating['log'])) : ?>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Object</th>
                                <th>Attribute</th>
                                <th>Value</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dataRating['log'] as $log) : ?>
                                <tr>
                                    <td><?= $log->getObjectName() ?></td>
                                    <td><?= $log->getAttributeName() ?></td>
                                    <td><?= $log->getValue() ?></td>
                                    <td><?= $log->getPoints() ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="text-right font-weight-bold">Total</td>
                                <td class="font-weight-bold"><?= $dataRating['points'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>
