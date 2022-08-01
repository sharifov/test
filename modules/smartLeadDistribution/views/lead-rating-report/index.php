<?php

use common\components\grid\project\ProjectColumn;
use common\components\grid\Select2Column;
use common\models\UserGroup;
use modules\smartLeadDistribution\src\SmartLeadDistribution;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use common\models\Lead;

/**
 * @var $this yii\web\View
 * @var $searchModel \modules\smartLeadDistribution\src\entities\LeadRatingReportSearch
 * @var array $reportDataList
 */

$this->title = 'Lead Rating summary report';
$this->params['breadcrumbs'][] = $this->title;
$total = 0;

?>
<?= $this->render('partial/_search-summary', ['model' => $searchModel]); ?>

<h1><?=\yii\helpers\Html::encode($this->title)?></h1>

<div class="lead-business-inbox">

    <?php Pjax::begin(['timeout' => 5000, 'scrollTo' => 0]); ?>

        <?php if (!empty($searchModel->ratingCategoryIds) && !empty($searchModel->leadStatusIds)) : ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center align-middle">
                            Category
                        </th>
                        <th colspan="<?= count($searchModel->leadStatusIds) ?>" class="text-center font-weight-bold">
                            Status
                        </th>
                    </tr>
                    <tr>
                        <?php foreach ($searchModel->leadStatusIds as $leadStatusId) : ?>
                            <th class="text-center">
                                <?= Lead::STATUS_LIST[$leadStatusId] ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($searchModel->ratingCategoryIds as $category) : ?>
                        <tr>
                            <td><?= SmartLeadDistribution::CATEGORY_LIST[$category] ?></td>
                            <?php foreach ($searchModel->leadStatusIds as $status) : ?>
                                <td class="text-center">
                                    <?php $leads = 0 ?>
                                    <?php /** @var array{status: int, leads_amount: int, category: int} $reportData */ ?>
                                    <?php foreach ($reportDataList as $reportData) : ?>
                                        <?php if ((int)$reportData['category'] === (int)$category && (int)$reportData['status'] === (int)$status) : ?>
                                            <?php $leads = $reportData['leads_amount'] ?>
                                            <?php $total += $leads; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?= $leads ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="<?= count($searchModel->leadStatusIds) ?>" class="text-right font-weight-bold">
                            Total
                        </td>
                        <td class="font-weight-bold text-center"><?= $total ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    <?php Pjax::end(); ?>
</div>
