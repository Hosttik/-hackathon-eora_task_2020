<?php

namespace backend\controllers;

use common\models\User;
use console\models\Adverts;
use Yii;

class AdvertsApiController extends BaseApiController
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    public function actionAdd()
    {
        if (Yii::$app->user->isGuest) {
            return $this->response->setError('Вы не авторизованы');
        }

        $post_config = [
            'title' => ['type' => 'string', 'required' => true],
            'description' => ['type' => 'string', 'required' => true],
            'cost' => ['type' => 'string', 'required' => true],
        ];
        $this->getRequestValidator()->validate($post_config, $this->request_post);
        if ($this->getRequestValidator()->getResponse()->hasErrors()) {
            return $this->response->setError(self::NOT_ENOUGH_PARAMETERS_ERROR_TEXT);
        }

        $this->request_post['cost'] = (int)$this->request_post['cost'];
        $advert = new Adverts();
        $advert->spec_user_id = Yii::$app->getUser()->getIdentity()->getId() ?? 0;
        $advert->title = $this->request_post['title'];
        $advert->description = $this->request_post['description'];
        $advert->cost = $this->request_post['cost'];
        if (!$advert->save()) {
            return $this->response->setError('Не удалось добавить объявление');
        }

        return $this->response;
    }

    public function actionGetAdvert()
    {
        $post_config = [
            'id' => ['type' => 'string', 'required' => true],
        ];
        $this->getRequestValidator()->validate($post_config, $this->request_post);
        if ($this->getRequestValidator()->getResponse()->hasErrors()) {
            return $this->response->setError(self::NOT_ENOUGH_PARAMETERS_ERROR_TEXT);
        }
        $advert = Adverts::findOne(['id' => (int)$this->request_post['id']]);

        return $this->response->setContent(['advert' => $advert]);
    }

    public function actionYourList()
    {
        if (Yii::$app->user->isGuest) {
            return $this->response->setError('Вы не авторизованы');
        }

        $spec_user_id = Yii::$app->getUser()->getIdentity()->getId() ?? 0;
        $adverts = Adverts::find()
            ->select(['title', 'description', 'cost', 'date_created'])
            ->where([
                'spec_user_id' => $spec_user_id,
            ])
            ->all();

        return $this->response->setContent(['adverts' => $adverts]);
    }

    public function actionSearchedList()
    {
        $post_config = [
            'query' => ['type' => 'string', 'required' => true],
        ];
        $this->getRequestValidator()->validate($post_config, $this->request_post);
        if ($this->getRequestValidator()->getResponse()->hasErrors()) {
            return $this->response->setError(self::NOT_ENOUGH_PARAMETERS_ERROR_TEXT);
        }

        $query = $this->request_post['query'];
        $adverts = Adverts::find()
            ->alias('a')
            ->select(['a.id', 'a.title', 'a.description as advert_description', 'a.cost', 'a.date_created', 'u.job_name', 'u.first_name', 'u.description as spec_description'])
            ->innerJoin('user as u', 'u.id = a.spec_user_id')
            ->where(['u.status' => User::STATUS_ACTIVE, 'u.type' => User::TYPE_SPECIALIST])
            ->andWhere(['OR',['like', 'LOWER(a.title)', strtolower($query)],['like', 'LOWER(a.description)', strtolower($query)]])
            ->asArray()
            ->all();
        $render_advert = [];
        $job_names = User::getJobNames();
        foreach ($adverts as $advert) {
            $advert['job_name'] = $job_names[$advert['job_name']] ?? 'Неизвестная специальность';
            $render_advert[] = $advert;
        }

        return $this->response->setContent(['adverts' => $render_advert]);
    }
}
