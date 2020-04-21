<?php

namespace modules\email\controllers\frontend;

use modules\email\src\protocol\gmail\GmailClient;
use sales\auth\Auth;
use Yii;
use modules\email\src\entity\emailAccount\EmailAccount;
use modules\email\src\entity\emailAccount\search\EmailAccountSearch;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class EmailAccountController extends Controller
{
    /**
    * @return array
    */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionRemoveAccessToken($id): Response
    {
        if (!$account = EmailAccount::findOne($id)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $account->removeGmailToken();
        if ($account->save()) {
            Yii::$app->session->setFlash('success', 'Access token was successfully removed.');
        } else {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString(['error' => $account->getErrors()]));
        }
        return $this->redirect(['email-account/view', 'id' => $account->ea_id]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionRequestAccessToken($id): Response
    {
        if (!$account = EmailAccount::findOne($id)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        Yii::$app->session->set('gmail_api_request_account_id', $account->ea_id);

        try {
            $url = (GmailClient::create())->createAuthUrl();
            return $this->redirect($url);
        } catch (\Throwable $e) {
            return $this->asJson(['message' => 'Google Client Error', 'error' => $e->getMessage()]);
        }
    }

    /**
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionAuthGmailCallback(): Response
    {
        $code = Yii::$app->request->get('code', null);
        if ($code !== null) {
            $code = (string)$code;
            $requestAccountId = (int)Yii::$app->session->get('gmail_api_request_account_id');
            if (!$requestAccountId) {
                throw new ForbiddenHttpException('Not found saved request account');
            }
            if (!$account = EmailAccount::findOne($requestAccountId)) {
                Yii::$app->session->remove('gmail_api_request_account_id');
                throw new ForbiddenHttpException('Request account not found');
            }
            try {
                $client = GmailClient::create();
                $accessToken = $client->fetchAccessTokenWithAuthCode($code);
                if (array_key_exists('error', $accessToken)) {
                    throw new \Exception(implode(', ', $accessToken));
                }
                $client->setAccessToken($accessToken);
                $account->ea_gmail_token = Json::encode($client->getAccessToken());
                if (!$account->save()) {
                    throw new \RuntimeException(VarDumper::dumpAsString(['account Id' => $account->ea_id, 'error' => $account->getErrors()]));
                }
                Yii::$app->session->remove('gmail_api_request_account_id');
                Yii::$app->session->setFlash('success', 'Access token was successfully saved');
                return $this->redirect(['email-account/view', 'id' => $account->ea_id]);
            } catch (\Throwable $e) {
                Yii::$app->session->remove('gmail_api_request_account_id');
                Yii::error($e, 'EmailAccountController:AuthGmailCallback');
                throw new ForbiddenHttpException('Server error');
            }
        }
        throw new ForbiddenHttpException('Invalid code');
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new EmailAccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

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
        $model = new EmailAccount();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ea_id]);
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
            return $this->redirect(['view', 'id' => $model->ea_id]);
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
     * @param integer $id
     * @return EmailAccount
     * @throws NotFoundHttpException
     */
    protected function findModel($id): EmailAccount
    {
        if (($model = EmailAccount::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
