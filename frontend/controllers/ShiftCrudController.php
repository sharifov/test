<?php

namespace frontend\controllers;

use modules\shiftSchedule\src\entities\shift\search\SearchShift;
use modules\shiftSchedule\src\entities\shift\Shift;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ShiftCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new SearchShift();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Shift();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->sh_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->sh_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param string|null $q
     * @param int|null $id
     * @return Response
     */
    public function actionAjaxList(?string $q = null, ?int $id = null): Response
    {
        $out = ['results' => ['id' => '', 'text' => '', 'selection' => '']];

        if ($q !== null) {
            $query = Shift::find();
            $data = $query->select(['id' => 'sh_id', 'text' => 'sh_name'])
                ->where(['like', 'sh_name', $q])
                ->orWhere(['sh_id' => (int) $q])
                ->limit(20)
                //->indexBy('id')
                ->asArray()
                ->all();

            if ($data) {
                foreach ($data as $n => $item) {
                    $text = $item['text'] . ' (' . $item['id'] . ')';
                    $data[$n]['text'] = self::formatText($text, $q);
                    $data[$n]['selection'] = $item['text'];
                }
            }

            $out['results'] = $data; //array_values($data);
        } elseif ($id > 0) {
            $shift = Shift::findOne($id);
            $out['results'] = ['id' => $id, 'text' => $shift->sh_name ?? '', 'selection' => $shift->sh_name ?? ''];
        }
        return $this->asJson($out);
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

    /**
     * @param integer $id
     * @return Shift
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Shift
    {
        if (($model = Shift::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
