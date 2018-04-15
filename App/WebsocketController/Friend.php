<?php
/**
 * Created by PhpStorm.
 * User: yuzhang
 * Date: 2018/4/14
 * Time: 下午3:40
 */

namespace App\WebsocketController;

use App\Exception\Websocket\FriendException;
use App\Model\User as UserModel;
use App\Service\FriendService;
use App\Service\UserCacheService;
use App\Task\Task;
use App\Model\Friend as FriendModel;
use EasySwoole\Core\Swoole\Task\TaskManager;

class Friend extends BaseWs
{
    /*
     * 发送好友请求
     * 1. 查看当前用户是存在/是否在线
     * 2. 发送好友请求
     */
    function sendReq(){
        $content = $this->request()->getArg('content');
        $user = $this->getUserInfo();
        $to_number = $content['number'];
        $res = $this->onlineValidate($to_number);
        if(!$res===true) {
            $this->response()->write(json_encode($res));
            return;
        }
        // 存储请求状态
        UserCacheService::saveFriendReq($user['user']['number'], $to_number);
        // 准备发送请求的数据
        $data = [
            'method'    => 'friendRequest',
            'data'      => [
                'from'  => $user['user']
            ]
        ];

        // 异步发送好友请求
        $fd = UserCacheService::getFdByNum($to_number);
        $taskData = [
            'method' => 'sendMsg',
            'data'  => [
                'fd'        => $fd,
                'data'      => $data
            ]
        ];
        $taskClass = new Task($taskData);
        TaskManager::async($taskClass);
        $this->sendMsg(['data'=>'好友请求已发送！']);
    }

    /*
     * 处理好友请求
     * @param number 对方号码
     * @param res    是否同意，1同意，0不同意
     */
    function doReq(){
        $content = $this->request()->getArg('content');
        $from_number = $content['number'];
        $check = $content['check'];
        $user = $this->getUserInfo();

        // 缓存校验，删除缓存，成功表示有该缓存记录，失败则没有
        $cache = UserCacheService::delFriendReq($from_number);
        if(!$cache){
            $msg = (new FriendException([
                'msg' => '好友请求操作失败',
                'errorCode' => 40003
            ]))->getMsg();
            $this->response()->write(json_encode($msg));
            return;
        }

        // 若同意，添加好友记录，异步通知双方，若不同意，在线则发消息通知
        if($check) {
            $from_user = UserModel::getUser(['number' => $from_number]);
            FriendModel::newFriend($user['user']['id'], $from_user['id']);
        }
        $taskData = [
            'method' => 'FriendOk',
            'data'  => [
                'from_number'   => $from_number,
                'number'        => $user['user']['number'],
                'check'         => $check
            ]
        ];
        $taskClass = new Task($taskData);
        TaskManager::async($taskClass);
    }

    /*
     * 获取好友列表
     */
    function getFriends(){
        $user = $this->getUserInfo();
        $friends = FriendModel::getAllFriends($user['user']['id']);
        $data = FriendService::getFriends($friends);
        $this->sendMsg(['data'=>$data]);
    }
}