<?php

namespace backend\controllers;

use console\models\Chat;
use Yii;

class ChatController extends BaseApiController
{

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    public function actionSaveMessage()
    {
        if (Yii::$app->user->isGuest) {
            return $this->response->setError('Вы не авторизованы');
        }

        $post_config = [
            'to_user_id' => ['type' => 'string', 'required' => true],
            'message' => ['type' => 'string', 'required' => true],
        ];
        $this->getRequestValidator()->validate($post_config, $this->request_post);
        if ($this->getRequestValidator()->getResponse()->hasErrors()) {
            return $this->response->setError(self::NOT_ENOUGH_PARAMETERS_ERROR_TEXT);
        }

        $chat_message = new Chat();
        $chat_message->from_user_id = Yii::$app->getUser()->getIdentity()->getId() ?? 0;
        $chat_message->to_user_id = (int)$this->request_post['to_user_id'];
        $chat_message->message = $this->request_post['message'];
        if (!$chat_message->save()) {
            return $this->response->setError('Не удалось отправить сообщение');
        }

        return $this->response;
    }

    public function actionGetMessages()
    {
        if (Yii::$app->user->isGuest) {
            return $this->response->setError('Вы не авторизованы');
        }

        $post_config = [
            'from_user_id' => ['type' => 'string', 'required' => true],
        ];
        $this->getRequestValidator()->validate($post_config, $this->request_post);
        if ($this->getRequestValidator()->getResponse()->hasErrors()) {
            return $this->response->setError(self::NOT_ENOUGH_PARAMETERS_ERROR_TEXT);
        }

        $current_user_id = Yii::$app->getUser()->getIdentity()->getId() ?? 0;
        $messages = Chat::find()
            ->select(['c.message', 'c.date_created', 'c.from_user_id', 'u.first_name', 'u.job_name'])
            ->alias('c')
            ->innerJoin('user as u', 'u.id = c.from_user_id')
            ->where([
                'from_user_id' => (int)$this->request_post['from_user_id'],
                'to_user_id' => $current_user_id,
            ])
            ->orWhere([
                'from_user_id' => $current_user_id,
                'to_user_id' => (int)$this->request_post['from_user_id'],
            ])
            ->asArray()
            ->all();

        return $this->response->setContent(['messages' => $messages]);
    }
}
