<?php


use sales\services\parsingDump\lib\ParsingDump;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $data array */
/* @var $dump string */
/* @var string $type */
/* @var string $gds */
/* @var bool|null $prepareSegment */

$this->title = 'Check GDS dump';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="check-flight-dump">

    <h3><?= Html::encode($this->title) ?></h3>


    <div class="col-md-4">
        <?= Html::beginForm() ?>
        <?= Html::dropDownList('gds', $gds,
            ParsingDump::GDS_TYPE_MAP,
            [
                'class' => 'form-control',
                'style' => 'margin-bottom: 12px;',
            ])
        ?>
        <?= Html::dropDownList('type', $type,
            ParsingDump::PARSING_TYPE_MAP,
            [
                'class' => 'form-control',
                'style' => 'margin-bottom: 12px;',
            ])
        ?>
        <?= Html::textarea('dump', $dump, ['rows' => 10, 'style' => 'width: 100%']) ?><br><br>

        <?php echo Html::checkbox('prepare_segment', $prepareSegment,
                    ['id' => 'prepare_segment', ]) ?> Reservation prepare segment<br><br>

        <?= Html::submitButton('Check Flight', ['class' => 'btn btn-primary']) ?>
        <?= Html::endForm() ?>
    </div>


    <div class="col-md-8">
        <?php echo (empty($data) && !empty($dump)) ? 'Parsing failed' : '' ?>

        <?php if ($data): ?>
            <h2>Parse dump: <?php echo $type ?>. GDS: <?php echo $gds ?> </h2>
            <pre>
                <?php \yii\helpers\VarDumper::dump($data, 10, true) ?>
            </pre>
        <?php endif; ?>
    </div>


</div>