<?php

/**
 * This is the template for generating a CRUD controller class file.
 */

use frontend\controllers\FController;
use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator common\components\gii\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

if ($generator->baseFrontendController) {
    $generator->baseControllerClass = FController::class;
}

$isCrud = false;
if ($generator->useLayoutCrud || stripos($controllerClass, 'crud') !== false) {
    $isCrud = true;
}

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)) : ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else : ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
<?php if ($isCrud) : ?>
use yii\helpers\ArrayHelper;
<?php endif; ?>

class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
<?php if ($isCrud) : ?>
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }
<?php else : ?>
    /**
    * @return array
    */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }
<?php endif; ?>

    /**
     * @return string
     */
    public function actionIndex(): string
    {
<?php if (!empty($generator->searchModelClass)) : ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
<?php else : ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
<?php foreach ($pks as $pk): ?>
                    <?= "'$pk' => SORT_DESC,\n" ?>
<?php endforeach; ?>
                ]
            ],
            */
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
<?php endif; ?>
    }

    /**
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(<?= $actionParams ?>): string
    {
        return $this->render('view', [
            'model' => $this->findModel(<?= $actionParams ?>),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new <?= $modelClass ?>();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', <?= $urlParams ?>]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', <?= $urlParams ?>]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(<?= $actionParams ?>): Response
    {
        $this->findModel(<?= $actionParams ?>)->delete();

        return $this->redirect(['index']);
    }

    /**
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?= $modelClass . "\n" ?>
     * @throws NotFoundHttpException
     */
    protected function findModel(<?= $actionParams ?>): <?= $modelClass  . "\n" ?>
    {
<?php
$condition = [];
foreach ($pks as $pk) {
    $condition[] = "'$pk' => \$$pk";
}
$condition = '[' . implode(', ', $condition) . ']';
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(<?= $generator->generateString('The requested page does not exist.') ?>);
    }
}
