<?php


namespace backend\controllers;

use app\classes\validators\RequestValidator;
//use app\controllers\adapters\Paginator;
use backend\classes\api\Response;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Class BaseController
 */
class BaseApiController extends Controller {

    const AJAX_ONLY_PERMITTED_ERROR = 'only ajax request are permitted';
    const POST_IS_EMPTY_ERROR = 'post data is empty';
    const AUTH_ERROR = 'access error';
    const NOT_ENOUGH_PARAMETERS_ERROR_TEXT = 'Not enough parameters';
    const DATA_SAVE_ERROR = 'Ошибка сохранения данных';

    const REQUEST_KEY_ERROR = 'request';

    /** @var Response */
    public $response;

    /** @var RequestValidator */
    private $request_validator;

//    /** @var Paginator */
//    private $paginator;

    protected $request_post;

    protected $request_get;

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
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
                'cors' => [
                    'Origin' => ['*']
                ],

            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->response = new Response();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

//         if (!\Yii::$app->request->isAjax) {
//             $this->response->addError(self::AJAX_ONLY_PERMITTED_ERROR, self::REQUEST_KEY_ERROR);
//         }

        $this->request_get = \Yii::$app->request->get();
        $this->request_post = \Yii::$app->request->post();
        $this->request_validator = new RequestValidator($this->response);

        return parent::beforeAction($action);
    }

    public function getRequestValidator(): RequestValidator
    {
        return $this->request_validator;
    }

    /**
     * @param $page
     * @param int $limit
     * @param $collection
     * @return Paginator
     * @throws \Exception
     */
//    protected function getPaginator($page, $collection, $sort_params = [], $limit = Paginator::DEFAULT_LIMIT)
//    {
//        if (!$this->paginator) {
//            $this->paginator = (new Paginator())->init($page, $collection, $sort_params, $limit);
//            if (null === $this->paginator) {
//                throw new \Exception('Collection instance Error');
//            }
//        }
//
//        return  $this->paginator;
//    }

//    protected function handleMailError(\Exception $e): void
//    {
//        parent::handleMailError($e);
//        $this->response->addError('Ошибка при постановке в очередь задания на отправку письма');
//    }
}
