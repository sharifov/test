<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $comForm CaseCommunicationForm
 * @var $model Cases
 * @var $previewEmailForm CasePreviewEmailForm
 * @var $previewSmsForm CasePreviewSmsForm
 * @var $isAdmin bool
 *
 */

use common\models\DepartmentEmailProject;
use common\models\DepartmentPhoneProject;
use frontend\models\CaseCommunicationForm;
use frontend\models\CasePreviewEmailForm;
use frontend\models\CasePreviewSmsForm;
use sales\entities\cases\Cases;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use yii\helpers\VarDumper;

$c_type_id = $comForm->c_type_id;

?>

	<div class="x_panel">
		<div class="x_title">
			<h2><i class="fa fa-comments"></i> Communication Log</h2>
			<ul class="nav navbar-right panel_toolbox">
				<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
				</li>
				<?php /*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>
            <li><a class="close-link"><i class="fa fa-close"></i></a>
            </li>*/?>
			</ul>
			<div class="clearfix"></div>
		</div>
		<div class="x_content" style="display: block;">
			<?php yii\widgets\Pjax::begin(['id' => 'pjax-case-communication-log' ,'enablePushState' => false]) ?>
			<?php /*<h1><?=random_int(1, 100)?></h1>*/ ?>
			<div class="panel">
				<div class="chat__list">

					<?= \yii\widgets\ListView::widget([
						'dataProvider' => $dataProvider,

						'options' => [
							'tag' => 'div',
							'class' => 'list-wrapper',
							'id' => 'list-wrapper',
						],
						'emptyText' => '<div class="text-center">Not found communication messages</div><br>',
						'layout' => "{summary}\n<div class=\"text-center\">{pager}</div>\n{items}<div class=\"text-center\">{pager}</div>\n",
						'itemView' => function ($model, $key, $index, $widget) use ($dataProvider) {
							return $this->render('_list_item_log',['model' => $model, 'dataProvider' => $dataProvider]);
						},

						'itemOptions' => [
							//'class' => 'item',
							'tag' => false,
						],

						/*'pager' => [
							'firstPageLabel' => 'first',
							'lastPageLabel' => 'last',
							'nextPageLabel' => 'next',
							'prevPageLabel' => 'previous',
							'maxButtonCount' => 3,
						],*/

					]) ?>
				</div>
			</div>

			<?php yii\widgets\Pjax::end() ?>
		</div>
	</div>