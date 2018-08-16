<?php
namespace common\controllers;

use common\models\Employee;
use common\models\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * Site controller
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        Yii::$app->setLayoutPath('@common/views/layouts');
        $this->layout = 'main';

        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'profile', 'get-airport'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $isBackend = strpos(Yii::$app->request->baseUrl, 'admin');

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login($isBackend)) {
            if (!$isBackend) {
                return $this->goBack('/');
            } else {
                return $this->goBack();
            }
        } else {
            $model->password = '';
            return $this->render('@common/views/login.php', [
                'model' => $model,
            ]);
        }
    }

    public function actionProfile()
    {
        if (!Yii::$app->user->isGuest) {
            $this->view->title = sprintf('Employee - Profile');
            $model = Employee::findOne(['id' => Yii::$app->user->identity->getId()]);
            if ($model !== null) {
                if (Yii::$app->request->isPost) {
                    $attr = Yii::$app->request->post($model->formName());
                    $model->prepareSave($attr);
                    if ($model->validate() && $model->save()) {
                        Yii::$app->getSession()->setFlash('success', 'Profile updated!');
                    }
                }

                return $this->render('@backend/views/employee/_form.php', [
                    'model' => $model,
                    'isProfile' => true
                ]);
            }
        }

        throw new ForbiddenHttpException();
    }

    public function actionGetAirport($term)
    {
        $response = file_get_contents(sprintf('%s?term=%s', Yii::$app->params['getAirportUrl'], $term));
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            return isset($response['data'])
                ? json_encode($response['data'])
                : json_encode([]);
        }

        return json_encode([]);
    }
}
