<?php

namespace frontend\controllers;

use sales\model\project\entity\params\Params;
use sales\widgets\ProjectSelect2Widget;
use Yii;
use common\models\Project;
use common\models\search\ProjectSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ProjectController implements the CRUD actions for Project model.
 */
class ProjectController extends FController
{

    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST']
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * Lists all Project models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Project model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Project();
        $model->p_params_json = Params::default();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->p_params_json) {
                $model->p_params_json = [];
            }
            try {
                $model->p_params_json = Json::decode($model->p_params_json);
            } catch (\Throwable $e) {
                Yii::$app->session->addFlash('error', 'Parameters: ' .  $e->getMessage());
                $model->p_params_json = [];
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Project model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $originalParams = $model->p_params_json;

        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->p_params_json = Json::decode($model->p_params_json);
            } catch (\Throwable $e) {
                Yii::$app->session->addFlash('error', 'Parameters: ' .  $e->getMessage());
                $model->p_params_json = $originalParams;
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        if (!$model->p_params_json) {
            $model->p_params_json = Params::default();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Project model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * @return Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionSynchronization()
    {
        $result = Project::synchronizationProjects();

        if ($result) {
            if ($result['error']) {
                Yii::$app->getSession()->setFlash('error', $result['error']);
            } else {
                $message = 'Synchronization successful<br>';
                if ($result['created']) {
                    $message .= 'Created projects: "' . implode(', ', $result['created']) . '"<br>';
                }
                if ($result['updated']) {
                    $message .= 'Updated projects: "' . implode(', ', $result['updated']) . '"<br>';
                }
                Yii::$app->getSession()->setFlash('success', $message);
            }
        }

        return $this->redirect(['project/index']);
    }

    public function actionListAjax(?string $q = null, ?int $id = null, ?string $selection = 'id')
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $out = ['results' => ['id' => '', 'name' => '', 'selection' => '']];

        ProjectSelect2Widget::checkSelection($selection);

        if ($q !== null) {
            $query = Project::find();
            $data = $query->select(['id', 'name'])
                ->where(['like', 'name', $q])
                ->orWhere(['id' => (int) $q])
                ->limit(20)
                //->indexBy('id')
                ->asArray()
                ->all();

            if ($data) {
                foreach ($data as $n => $item) {
                    $text = $item['name'] . ' (' . $item['id'] . ')';
                    $data[$n]['text'] = self::formatText($text, $q);
                    $data[$n]['selection'] = $item[$selection];
                }
            }

            $out['results'] = $data; //array_values($data);
        } elseif ($id > 0) {
            $project = Project::findOne($id);
            $out['results'] = ['id' => $id, 'text' => $project ? $project->name : '', 'selection' => $project ? $project->{$selection} : ''];
        }
        return $out;
    }

    /**
     * @param string $str
     * @param string $term
     * @return string
     */
    private static function formatText(string $str, string $term): string
    {
        return preg_replace('~' . $term . '~i', '<b style="color: #e15554"><u>$0</u></b>', $str);
    }
}
