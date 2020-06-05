<?php


namespace backend\controllers;


use common\models\ClientRegisterForm;
use common\models\LoginForm;
use common\models\SpecialistRegisterForm;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class SiteApiController extends BaseApiController
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'specialist-register', 'user-register', 'get-job-names', 'get-types', 'get-user-info'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => ['*']
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
            ],
        ];
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->response->setError('Вы уже авторизованы');
        }

        $post_config = [
            'email' => ['type' => 'string', 'required' => true],
            'password' => ['type' => 'string', 'required' => true],
        ];

        $this->getRequestValidator()->validate($post_config, $this->request_post);
        if ($this->getRequestValidator()->getResponse()->hasErrors()) {
            return $this->response->setError(self::NOT_ENOUGH_PARAMETERS_ERROR_TEXT);
        }

        $model = new LoginForm();
        if (!$model->load(['LoginForm' => $this->request_post]) || !$model->login()) {
            return $this->response->setError('Неверная комбинация логин/пароля');
        }

        return $this->response;
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionSpecialistRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->response->setError('Вы уже авторизованы');
        }

        $post_config = [
            'email' => ['type' => 'string', 'required' => true],
            'job_name' => ['type' => 'string', 'required' => true],
            'first_name' => ['type' => 'string', 'required' => true],
            'description' => ['type' => 'string', 'required' => false],
            'password' => ['type' => 'string', 'required' => true],
        ];


        $this->getRequestValidator()->validate($post_config, $this->request_post);
        if ($this->getRequestValidator()->getResponse()->hasErrors()) {
            return $this->response->setError(self::NOT_ENOUGH_PARAMETERS_ERROR_TEXT);
        }
        $this->request_post['job_name'] = (int)$this->request_post['job_name'];

        $model = new SpecialistRegisterForm();
        if (!$model->load(['SpecialistRegisterForm' => $this->request_post]) || !$model->register()) {
            return $this->response->setError('Ошибка регистрации');
        }

        return $this->response;
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionUserRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->response->setError('Вы уже авторизованы');
        }

        $post_config = [
            'email' => ['type' => 'string', 'required' => true],
            'first_name' => ['type' => 'string', 'required' => true],
            'password' => ['type' => 'string', 'required' => true],
        ];

        $this->getRequestValidator()->validate($post_config, $this->request_post);
        if ($this->getRequestValidator()->getResponse()->hasErrors()) {
            return $this->response->setError(self::NOT_ENOUGH_PARAMETERS_ERROR_TEXT);
        }

        $model = new ClientRegisterForm();
        if (!$model->load(['ClientRegisterForm' => $this->request_post]) || !$model->register()) {
            return $this->response->setError('Ошибка регистрации');
        }

        return $this->response;
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionGetJobNames()
    {
        return $this->response->setContent(['job_names' => User::getJobNames()]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->response;
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionGetTypes()
    {
        return $this->response->setContent(['types' => User::getTypes()]);
    }

    public function actionGetUserInfo()
    {
        $is_guest = true;
        $type = null;
        $user_id = null;
        if (!Yii::$app->user->isGuest) {
            $is_guest = false;
            $user_id = Yii::$app->getUser()->getIdentity()->getId() ?? 0;
            $user = User::findIdentity($user_id);
            $type = $user->type ?? null;
        }

        return $this->response->setContent(['user_id' => $user_id, 'is_guest' => $is_guest, 'type' => $type]);
    }
}
