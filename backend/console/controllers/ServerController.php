<?php


namespace console\controllers;

use common\models\User;
use yii\console\Controller;
use jones\wschat\components\Chat;
use jones\wschat\components\ChatManager;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class ServerController extends Controller
{
    public function actionRun()
    {
//        $manager = \Yii::configure(new ChatManager(), [
//            'userClassName' => User::class //allow to get users from MySQL or PostgreSQL
//        ]);

        $server = IoServer::factory(new HttpServer(new WsServer(new Chat(new ChatManager()))), 8080);
        $server->run();
    }
}