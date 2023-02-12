<?php

namespace App\Http\Controller\Api;

use App\Lib\MyCode;
use App\Lib\MyCommon;
use App\Lib\MyQuit;
use App\Model\Data\PaginationData;
use App\Model\Entity\Notification;
use Swoft\Db\DB;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;
use Swoft\Redis\Redis;
use Swoft\Stdlib\Helper\JsonHelper;
use App\Http\Middleware\AuthMiddleware;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;
use App\Http\Middleware\BaseMiddleware;

/**
 *
 * Class NotificationController
 * @package App\Http\Controller\Api
 * @Controller("v1/notification")
 * @Middleware(AuthMiddleware::class)
 */
class NotificationController
{
    /**
     * 消息通知
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function notification_list(Request $request)
    {
        $has_notification = false;
        $exists = Notification::where(['user_id' => $request->uid, 'is_read' => 0])->exists();
        if ($exists === true) {
            $has_notification = true;
        }
        //评论消息数
        $comment_count = Notification::where(['user_id' => $request->uid, 'is_read' => 0, 'action' => 'comment'])->count();
        //他人赞我数
        $like_count = Notification::where(['user_id' => $request->uid, 'is_read' => 0, 'action' => 'like'])->count();
        $data = [
            'has_notification' => $has_notification,
            'comment_count'    => $comment_count,
            'like_count'       => $like_count
        ];
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 评论消息-他人评论
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function other_comment_list(Request $request)
    {
        $params = $request->get();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? config('page_num');
        $data = PaginationData::table('comment as co')
            ->select('co.id', 'co.parent_comment_id', 'co.reply_id', 'co.user_id', 'ub.nickname', 'ub.user_pic', 'co.created_at', 'co.content', 'co.news_id')
            ->leftJoin('user_basical_info as ub', 'co.user_id', '=', 'ub.id')
            ->where(['co.receive_user_id' => $request->uid])
            ->forPage($page, $size)
            ->orderBy('co.created_at', 'desc')
            ->get();
        foreach ($data['data'] as $key => $val) {
            $data['data'][$key]['user_pic'] = !empty($val['user_pic']) ? MyCommon::get_filepath($val['user_pic']) : '';
            //我的评论
            if (empty($val['reply_id'])) {
                $data['data'][$key]['my_comment_content'] = DB::table('comment')->where(['id' => $val['parent_comment_id']])->value('content');
            } else {
                $data['data'][$key]['my_comment_content'] = DB::table('comment')->where(['id' => $val['reply_id']])->value('content');
            }
        }
        //消息已读
        Notification::where([
            'user_id'     => $request->uid,
            'type'        => 'remind',
            'target_type' => 'comment',
            'action'      => 'comment',
            'is_read'     => 0
        ])->update(['is_read' => 1]);
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 评论消息-我的评论
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function comment_list(Request $request)
    {
        $params = $request->get();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? config('page_num');
        $data = PaginationData::table('comment as co')
            ->select('co.id', 'co.parent_comment_id', 'co.reply_id', 'ub.nickname', 'ub.user_pic', 'rub.nickname as receive_nickname', 'co.created_at', 'co.content', 'co.news_id', 'ne.title', 'ne.thumbnail')
            ->leftJoin('user_basical_info as ub', 'co.user_id', '=', 'ub.id')
            ->leftJoin('user_basical_info as rub', 'co.receive_user_id', '=', 'rub.id')
            ->leftJoin('news as ne', 'co.news_id', '=', 'ne.id')
            ->where(['co.user_id' => $request->uid])
            ->forPage($page, $size)
            ->orderBy('co.created_at', 'desc')
            ->get();
        foreach ($data['data'] as $key => $val) {
            $data['data'][$key]['user_pic'] = !empty($val['user_pic']) ? MyCommon::get_filepath($val['user_pic']) : '';
            $data['data'][$key]['thumbnail'] = !empty($val['thumbnail']) ? MyCommon::get_filepath($val['thumbnail']) : '';
            //他人评论
            if (empty($val['reply_id'])) {
                $data['data'][$key]['other_comment_content'] = DB::table('comment')->where(['id' => $val['parent_comment_id']])->value('content');
            } else {
                $data['data'][$key]['other_comment_content'] = DB::table('comment')->where(['id' => $val['reply_id']])->value('content');
            }
        }
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 他人点赞-评论
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function other_like_comment_list(Request $request)
    {
        $params = $request->get();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? config('page_num');
        $data = PaginationData::table('like as li')
            ->select('ub.nickname', 'ub.user_pic', 'li.created_at', 'co.content', 'co.id as comment_id', 'co.news_id')
            ->leftJoin('comment as co', 'co.id', '=', 'li.target_id')
            ->leftJoin('user_basical_info as ub', 'li.user_id', '=', 'ub.id')
            ->where(['li.target_type' => 'comment', 'li.receive_user_id' => $request->uid])
            ->forPage($page, $size)
            ->orderBy('li.created_at', 'desc')
            ->get();
        foreach ($data['data'] as $key => $val) {
            $data['data'][$key]['user_pic'] = !empty($val['user_pic']) ? MyCommon::get_filepath($val['user_pic']) : '';
        }
        //消息已读
        Notification::where([
            'user_id'     => $request->uid,
            'type'        => 'remind',
            'target_type' => 'comment',
            'action'      => 'like',
            'is_read'     => 0
        ])->update(['is_read' => 1]);
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 我的点赞-文章
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function like_news_list(Request $request)
    {
        $params = $request->get();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? config('page_num');
        $data = PaginationData::table('like as li')
            ->select('ne.id', 'ne.title', 'ne.thumbnail', 'ne.content', 'ne.view_count', 'ne.created_at')
            ->leftJoin('news as ne', 'ne.id', '=', 'li.target_id')
            ->where(['target_type' => 'news', 'user_id' => $request->uid])
            ->forPage($page, $size)
            ->orderBy('li.created_at', 'desc')
            ->get();
        foreach ($data['data'] as $key => $val) {
            $data['data'][$key]['thumbnail'] = !empty($val['thumbnail']) ? MyCommon::get_filepath($val['thumbnail']) : '';
        }
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 我的点赞-评论
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function like_comment_list(Request $request)
    {
        $params = $request->get();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? config('page_num');
        $data = PaginationData::table('like as li')
            ->select('ub.nickname', 'ub.user_pic', 'li.created_at', 'ubi.nickname as cover_nickname', 'co.content', 'co.id as comment_id', 'co.news_id')
            ->leftJoin('comment as co', 'co.id', '=', 'li.target_id')
            ->leftJoin('user_basical_info as ub', 'li.user_id', '=', 'ub.id')
            ->leftJoin('user_basical_info as ubi', 'li.receive_user_id', '=', 'ubi.id')
            ->where(['li.target_type' => 'comment', 'li.user_id' => $request->uid])
            ->forPage($page, $size)
            ->orderBy('li.created_at', 'desc')
            ->get();
        foreach ($data['data'] as $key => $val) {
            $data['data'][$key]['user_pic'] = !empty($val['user_pic']) ? MyCommon::get_filepath($val['user_pic']) : '';
        }
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

}
