<?php

namespace frontend\controllers;

use common\models\search\EmailSearch;
use src\entities\email\Email;
use src\entities\email\EmailSearch as EmailNormalizedSearch;
use src\model\email\useCase\send\EmailSenderService;
use src\dispatchers\EventDispatcher;
use src\services\email\EmailsNormalizeService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\email\src\abac\dto\EmailAbacDto;
use modules\email\src\abac\EmailAbacObject;
use yii\web\ForbiddenHttpException;
use yii\bootstrap\Html;

/**
 * EmailNormalizedController implements the CRUD actions for Email model.
 *
 * @property EmailSenderService $emailSender
 * @property EventDispatcher $eventDispatcher
 */
class EmailNormalizedController extends FController
{
    private $emailSender;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct($id, $module, EmailSenderService $emailSender, EventDispatcher $eventDispatcher, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->emailSender = $emailSender;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'allowActions' => [
                    'view',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all Email models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmailNormalizedSearch();

        $params = Yii::$app->request->queryParams;
        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['EmailSearch']['supervision_id'] = Yii::$app->user->id;
        }
        $searchModel->date_range = null;

        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $model = $this->findModel($id);
        /** @abac new EmailAbacDto($model), EmailAbacObject::ACT_VIEW, EmailAbacObject::ACTION_ACCESS, Restrict access to view emails on case or lead*/

/*         if (!Yii::$app->abac->can(new EmailAbacDto($model), EmailAbacObject::ACT_VIEW, EmailAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access denied.');
        }
 */
        if (Yii::$app->request->get('preview')) {
            return $model->getEmailBodyHtml() ?: '';
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): \yii\web\Response
    {
        $email = $this->findModel($id);
        $email->delete();
        $this->eventDispatcher->dispatchAll($email->releaseEvents());

        return $this->redirect(['index']);
    }

    /**
     * Finds the Email model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Email the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Email
    {
        if (($model = Email::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSoftDelete($id): \yii\web\Response
    {
        $model = $this->findModel($id);
        $model->e_is_deleted = (int) ! $model->e_is_deleted;
        $model->save();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionNormalize($id)
    {
        $result = [
            'success' => false,
            'errors' => [],
        ];

        if (($emailOld = \common\models\Email::findOne($id)) !== null) {
            $service = new EmailsNormalizeService();
            $service->createEmailFromOld($emailOld);

            if ($service->email !== null) {
                $result['success'] =  true;
                $result['html'] = Html::a('<span class="label label-success">yes</span>', ['email-normalized/view', 'id' => $service->email->e_id], ['target' => '_blank', 'data-pjax' => 0]);
            }
            else {
                $result['errors'] = $service->getErrors();
            }
        }


        return $this->asJson($result);
    }
}
