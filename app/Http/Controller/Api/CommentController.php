<?php

namespace App\Http\Controller\Api;

use App\lib\MyCode;
use App\Lib\MyCommon;
use App\Lib\MyQuit;
use App\Model\Data\PaginationData;
use App\Model\Entity\Like;
use App\Model\Entity\News;
use App\Model\Entity\Comment;
use App\Model\Entity\Notification;
use App\Model\Entity\UserBasicalInfo;
use App\Rpc\Lib\AuthInterface;
use Swoft\Db\DB;
use Swoft\Db\Exception\DbException;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\Middlewares;
use App\Http\Middleware\AuthMiddleware;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Validator\Annotation\Mapping\Validate;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;

/**
 * Class CommentController
 * @package App\Http\Controller\Api
 * @Controller(prefix="/v1/comment")
 */
class CommentController
{
    /**
     * @Reference(pool="auth.pool")
     * @var AuthInterface
     */
    private $authService;

    /**
     * 评论添加
     * @param Request $request
     * @return array
     * @Validate(validator="CommentValidator",fields={"receive_user_id", "parent_comment_id", "reply_id", "news_id", "content"})
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @Middleware(AuthMiddleware::class)
     * @RequestMapping(method={RequestMethod::POST})
     */
    public function add(Request $request)
    {
        $params = $request->post();
        DB::beginTransaction();
        try{
            $model = new Comment();
            $model->setUserId($request->uid);
            $model->setReceiveUserId($params['receive_user_id']);
            $model->setParentCommentId($params['parent_comment_id']);
            $model->setReplyId($params['reply_id']);
            $model->setNewsId($params['news_id']);
            $model->setContent($params['content']);
            $res = $model->save();
            if (!$res) {
                throw new DbException('新增评论失败');
            }
            //消息通知-他人评论
            if (!empty($params['receive_user_id'])) {
                $nickname = UserBasicalInfo::where(['id' => $request->uid])->value('nickname');
                $notification = new Notification();
                $notification->setUserId($params['receive_user_id']);
                $notification->setType('remind');
                $notification->setTargetId($model->getId());
                $notification->setTargetType('comment');
                $notification->setAction('comment');
                $notification->setSenderId($request->uid);
                $notification->setSenderType('user');
                $notification->setIsRead(0);
                $notification->setContent($nickname . '回复了你的评论');
                $notification_res = $notification->save();
                if (!$notification_res) {
                    throw new DbException('新增消息通知失败');
                }
            }
            //增加评论数
            News::where(['id' => $params['news_id']])->increment('comment_count');
            DB::commit();
            return MyQuit::returnMessage(MyCode::SUCCESS, '成功');
        } catch (DbException $e) {
            DB::rollBack();
            return MyQuit::returnMessage(MyCode::SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * 评论列表
     * @param Request $request
     * @return array
     * @Validate(validator="CommentValidator",fields={"news_id"})
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function list(Request $request)
    {
        $params = $request->get();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? config('page_num');
        //点赞状态
        $token = $request->getHeaderLine('token');
        $is_like = false;
        if (empty($token)) {
            $is_like = false;
        } else {
            //验证登录
            $res_data = $this->authService->verify_login($token, $request->client_type, $request->device_id);
            if ($res_data == false) {
                return MyQuit::returnMessage(MyCode::LOGIN_EXPIRE, '登录已过期');
            }
        }
        $data = PaginationData::table('comment as co')
            ->select('co.*', 'ub.nickname', 'ub.user_pic')
            ->where(['news_id' => $params['news_id'], 'parent_comment_id' => 0])
            ->leftJoin('user_basical_info as ub', 'co.user_id', '=', 'ub.id')
            ->forPage($page, $size)
            ->orderBy('co.created_at', 'desc')
            ->get();
        foreach ($data['data'] as $key => $val) {
            $data['data'][$key]['user_pic'] = !empty($val['user_pic']) ? MyCommon::get_filepath($val['user_pic']) : '';
            if (empty($token)) {
                $data['data'][$key]['is_like'] = $is_like;
            } else {
                //判断点赞状态
                $like_exists = Like::where(['user_id' => $res_data['id'], 'target_id' => $val['id'], 'target_type' => 'comment'])->exists();
                if ($like_exists === true) {
                    $data['data'][$key]['is_like'] = true;
                } else {
                    $data['data'][$key]['is_like'] = false;
                }
            }
            //处理评论回复
            $reply_list = DB::table('comment as co')
                ->select('co.*', 'sub.nickname', 'sub.user_pic', 'rub.nickname as receive_nickname')
                ->leftJoin('user_basical_info as sub', 'co.user_id', '=', 'sub.id')
                ->leftJoin('user_basical_info as rub', 'co.receive_user_id', '=', 'rub.id')
                ->where(['co.parent_comment_id' => $val['id']])
                ->orderBy('co.created_at', 'desc')
                ->get();
            if (!empty($reply_list)) {
                $reply_list = $reply_list->toArray();
                foreach ($reply_list as $key1 => $val1) {
                    $reply_list[$key1]['user_pic'] = !empty($val1['user_pic']) ? MyCommon::get_filepath($val1['user_pic']) : '';
                    if (empty($token)) {
                        $reply_list[$key1]['is_like'] = $is_like;
                    } else {
                        //判断点赞状态
                        $like_exists = Like::where(['user_id' => $res_data['id'], 'target_id' => $val1['id'], 'target_type' => 'comment'])->exists();
                        if ($like_exists === true) {
                            $reply_list[$key1]['is_like'] = true;
                        } else {
                            $reply_list[$key1]['is_like'] = false;
                        }
                    }
                }
                $data['data'][$key]['reply_list'] = $reply_list;
            } else {
                $data['data'][$key]['reply_list'] = [];
            }
        }
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

}
