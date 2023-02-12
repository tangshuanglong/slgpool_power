<?php

namespace App\Http\Controller\Api;

use App\lib\MyCode;
use App\Lib\MyCommon;
use App\Lib\MyQuit;
use App\Model\Data\PaginationData;
use App\Model\Entity\Comment;
use App\Model\Entity\GoodBad;
use App\Model\Entity\News;
use App\Model\Entity\Notification;
use App\Model\Entity\Search;
use App\Model\Entity\UserBasicalInfo;
use App\Rpc\Lib\AuthInterface;
use Swoft\Db\DB;
use Swoft\Db\Eloquent\Model;
use Swoft\Db\Exception\DbException;
use App\Model\Entity\Like;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\Middlewares;
use App\Http\Middleware\AuthMiddleware;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Redis\Redis;
use Swoft\Validator\Annotation\Mapping\Validate;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;

/**
 * Class NewsController
 * @package App\Http\Controller\Api
 * @Controller(prefix="/v1/news")
 * @Middlewares({
 * })
 */
class NewsController
{
    /**
     * @Inject()
     * @var MyCommon
     */
    private $myCommon;

    /**
     * @Reference(pool="auth.pool")
     * @var AuthInterface
     */
    private $authService;

    /**
     * 热门搜索
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function hot_search(Request $request)
    {
        $data = DB::table('search')->orderBy('search_count', 'desc')->limit(8)->get();
        if(!empty($data)){
            $data = $data->toArray();
        }
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 资讯列表
     * @param Request $request
     * @return array
     * @Validate(validator="NewsValidator",fields={"news_type"})
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function list(Request $request)
    {
        $params = $request->get();
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? config('page_num');
        //利好、利空状态
        $token = $request->getHeaderLine('token');
        $is_good = false;
        $is_bad = false;
        if (empty($token)) {
            $is_good = false;
            $is_bad = false;
        } else {
            //验证登录
            $res_data = $this->authService->verify_login($token, $request->client_type, $request->device_id);
            if ($res_data == false) {
                return MyQuit::returnMessage(MyCode::LOGIN_EXPIRE, '登录已过期');
            }
        }
        $where = [
            ['status', '=', 1],
            ['news_type', '=', $params['news_type']]
        ];
        $orwhere = [
            ['status', '=', 1],
            ['news_type', '=', $params['news_type']]
        ];
        if (!empty($params['keywords'])) {//搜索
            $params['keywords'] = trim($params['keywords']);
            $where[] = ['title', 'like', '%' . $params['keywords'] . '%'];
            $orwhere[] = ['content', 'like', '%' . $params['keywords'] . '%'];
        } else {
            $where[] = ['is_featured', '=', 2];
        }
        $data = PaginationData::table('news')
            ->select('id', 'title', 'thumbnail', 'content', 'view_count', 'comment_count', 'like_count', 'good_count', 'bad_count', 'created_at')
            ->where($where)
            ->orWhere($orwhere)
            ->forPage($page, $size)
            ->orderBy('order_num', 'desc')
            ->get();
        foreach ($data['data'] as $key => $val) {
            $data['data'][$key]['thumbnail'] = !empty($val['thumbnail']) ? MyCommon::get_filepath($val['thumbnail']) : '';
            if (empty($token)) {
                $data['data'][$key]['is_good'] = $is_good;
                $data['data'][$key]['is_bad'] = $is_bad;
            } else {
                // 判断利好、利空状态
                $good_exists = GoodBad::where(['user_id' => $res_data['id'], 'news_id' => $val['id'], 'type' => 'good'])->exists();
                $bad_exists = GoodBad::where(['user_id' => $res_data['id'], 'news_id' => $val['id'], 'type' => 'bad'])->exists();
                if ($good_exists === true) {
                    $data['data'][$key]['is_good'] = true;
                } else {
                    $data['data'][$key]['is_good'] = false;
                }
                if ($bad_exists === true) {
                    $data['data'][$key]['is_bad'] = true;
                } else {
                    $data['data'][$key]['is_bad'] = false;
                }
            }
        }
        //精选
        $featured = DB::table('news')
            ->select('id', 'title', 'thumbnail', 'view_count', 'comment_count', 'like_count', 'good_count', 'bad_count', 'created_at')
            ->where(['is_featured' => 1, 'news_type' => 'article'])
            ->orderBy('order_num', 'desc')->get();
        if (!empty($featured)) {
            $featured = $featured->toArray();
            foreach ($featured as $key => $val) {
                $featured[$key]['thumbnail'] = !empty($val['thumbnail']) ? MyCommon::get_filepath($val['thumbnail']) : '';
            }
        }
        if (!empty($params['keywords'])) {//搜索
            $data['featured'] = [];
            //统计搜索次数
            $exists = Search::where(['keywords' => $params['keywords']])->exists();
            if ($exists) {
                Search::where(['keywords' => $params['keywords']])->increment('search_count');
            } else {
                $search = new Search();
                $search->setKeywords($params['keywords']);
                $search->setSearchCount(1);
                $search->save();
            }
        } else {
            $data['featured'] = $featured;
        }
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 资讯详情
     * @param Request $request
     * @return array
     * @Validate(validator="NewsValidator",fields={"id"})
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function details(Request $request)
    {
        $params = $request->get();
        //增加浏览量
        $ip = MyCommon::get_ip($request);
        $key = 'isView_' . $params['id'] . '_' . $ip;
        if (!Redis::exists($key)) {
            Redis::set($key, 1, 3600);
            DB::table('news')->where(['id' => $params['id']])->increment('view_count');
        }
        $data = DB::table('news')->where(['status' => 1, 'id' => $params['id']])->firstArray();
        if (empty($data)) {
            return MyQuit::returnMessage(MyCode::SERVER_ERROR, '未找到此资讯');
        }
        //处理图片
        $data['thumbnail'] = !empty($data['thumbnail']) ? MyCommon::get_filepath($data['thumbnail']) : '';
        //点赞状态
        $token = $request->getHeaderLine('token');
        if (empty($token)) {
            $data['is_like'] = false;
            $data['is_good'] = false;
            $data['is_bad'] = false;
        } else {
            //验证登录
            $res_data = $this->authService->verify_login($token, $request->client_type, $request->device_id);
            if ($res_data == false) {
                return MyQuit::returnMessage(MyCode::LOGIN_EXPIRE, '登录已过期');
            }
            //判断点赞状态
            $like_exists = Like::where(['user_id' => $res_data['id'], 'target_id' => $params['id'], 'target_type' => 'news'])->exists();
            if ($like_exists === true) {
                $data['is_like'] = true;
            } else {
                $data['is_like'] = false;
            }
            // 判断利好、利空状态
            $good_exists = GoodBad::where(['user_id' => $res_data['id'], 'news_id' => $params['id'], 'type' => 'good'])->exists();
            $bad_exists = GoodBad::where(['user_id' => $res_data['id'], 'news_id' => $params['id'], 'type' => 'bad'])->exists();
            if ($good_exists === true) {
                $data['is_good'] = true;
            } else {
                $data['is_good'] = false;
            }
            if ($bad_exists === true) {
                $data['is_bad'] = true;
            } else {
                $data['is_bad'] = false;
            }
        }
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 相关推荐
     * @param Request $request
     * @return array
     * @Validate(validator="NewsValidator",fields={"id"})
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @RequestMapping(method={RequestMethod::GET})
     */
    public function recommend(Request $request)
    {
        $where = [
            'news_type'   => 'article',
            'status'      => 1
        ];
        $data = DB::table('news')
            ->select('id', 'title', 'thumbnail', 'view_count', 'created_at')
            ->where($where)
            ->orderBy('order_num', 'desc')
            ->limit(3)
            ->get();
        if ($data) {
            $data = $data->toArray();
            foreach ($data as $key => $val) {
                $data[$key]['thumbnail'] = !empty($val['thumbnail']) ? MyCommon::get_filepath($val['thumbnail']) : '';
            }
        }
        return MyQuit::returnSuccess($data, MyCode::SUCCESS, 'success');
    }

    /**
     * 点赞
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @Validate(validator="NewsValidator",fields={"receive_user_id", "target_id", "target_type", "like_action_type"})
     * @Middleware(AuthMiddleware::class)
     * @RequestMapping(method={RequestMethod::POST})
     */
    public function like(Request $request)
    {
        $params = $request->post();
        $model = new Like();
        $like_content = '';
        //判断目标id是否存在
        if ($params['target_type'] == 'news') {//资讯
            $news_exists = News::where(['id' => $params['target_id']])->exists();
            if ($news_exists == false) {
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '资讯不存在');
            }
            $like_content = '文章';
        } elseif ($params['target_type'] == 'comment') {//评论
            $comment_exists = Comment::where(['id' => $params['target_id']])->exists();
            if ($comment_exists == false) {
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '评论不存在');
            }
            $like_content = '评论';
        }
        $like_info = $model->where(['user_id' => $request->uid, 'target_id' => $params['target_id'], 'target_type' => $params['target_type']])->first();
        //判断点赞
        if ($params['like_action_type'] == 'like') {
            //判断是否重复点赞
            if (!empty($like_info)) {
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '重复点赞操作');
            }
        } elseif ($params['like_action_type'] == 'not_like') {
            //判断是否有点赞
            if (empty($like_info)) {
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '此赞不存在');
            }
        }
        DB::beginTransaction();
        try {
            if ($params['like_action_type'] == 'like') {
                if ($params['target_type'] == 'news') {//资讯
                    //资讯增加点赞数
                    News::where(['id' => $params['target_id']])->increment('like_count');
                } elseif ($params['target_type'] == 'comment') {//评论
                    //评论增加点赞数
                    Comment::where(['id' => $params['target_id']])->increment('like_count');
                }
                $model->setUserId($request->uid);
                $model->setReceiveUserId($params['receive_user_id']);
                $model->setTargetId($params['target_id']);
                $model->setTargetType($params['target_type']);
                $res = $model->save();
                //消息通知-他人点赞-评论
                if (!empty($params['receive_user_id'])) {
                    $nickname = UserBasicalInfo::where(['id' => $request->uid])->value('nickname');
                    $notification = new Notification();
                    $notification->setUserId($params['receive_user_id']);
                    $notification->setType('remind');
                    $notification->setTargetId($model->getId());
                    $notification->setTargetType($params['target_type']);
                    $notification->setAction('like');
                    $notification->setSenderId($request->uid);
                    $notification->setSenderType('user');
                    $notification->setIsRead(0);
                    $notification->setContent($nickname . '点赞了你的' . $like_content);
                    $notification_res = $notification->save();
                    if (!$notification_res) {
                        throw new DbException('新增消息通知失败');
                    }
                }
            } elseif ($params['like_action_type'] == 'not_like') {
                if ($params['target_type'] == 'news') {//资讯
                    //资讯减少点赞数
                    News::where(['id' => $params['target_id']])->decrement('like_count');
                } elseif ($params['target_type'] == 'comment') {//评论
                    //评论减少点赞数
                    Comment::where(['id' => $params['target_id']])->decrement('like_count');
                }
                $res = $model->where(['id' => $like_info['id']])->delete();
                //删除消息通知-他人点赞-评论
                Notification::where([
                    'user_id'     => $params['receive_user_id'],
                    'type'        => 'remind',
                    'target_id'   => $like_info['id'],
                    'target_type' => $params['target_type'],
                    'action'      => 'like',
                    'sender_id'   => $request->uid,
                    'sender_type' => 'user'
                ])->delete();
            }
            if ($res) {
                DB::commit();
                return MyQuit::returnMessage(MyCode::SUCCESS, 'success');
            } else {
                DB::rollBack();
                return MyQuit::returnMessage(MyCode::SERVER_ERROR, 'server error');
            }
        } catch (DbException $e) {
            DB::rollBack();
            return MyQuit::returnMessage(MyCode::SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * 利好利空
     * @param Request $request
     * @return array
     * @throws \Swoft\Db\Exception\DbException
     * @throws \Swoft\Validator\Exception\ValidatorException
     * @Validate(validator="NewsValidator",fields={"news_id", "good_bad_action_type"})
     * @Middleware(AuthMiddleware::class)
     * @RequestMapping(method={RequestMethod::POST})
     */
    public function good_bad(Request $request)
    {
        $params = $request->post();
        $model = new GoodBad();
        $news_exists = News::where(['id' => $params['news_id']])->exists();
        if ($news_exists == false) {
            return MyQuit::returnMessage(MyCode::PARAM_ERROR, '资讯不存在');
        }
        $good_info = $model->where(['user_id' => $request->uid, 'news_id' => $params['news_id'], 'type' => 'good'])->first();
        $bad_info = $model->where(['user_id' => $request->uid, 'news_id' => $params['news_id'], 'type' => 'bad'])->first();
        if ($params['good_bad_action_type'] == 'good') {//利好
            //判断是否重复利好
            if (!empty($good_info)) {
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '重复利好操作');
            }
            //资讯增加利好量
            News::where(['id' => $params['news_id']])->increment('good_count');
            //增加利好记录
            $model->setUserId($request->uid);
            $model->setNewsId($params['news_id']);
            $model->setType('good');
            $res = $model->save();
            //同步利空操作
            if (!empty($bad_info)) {
                //资讯减少利空量
                News::where(['id' => $params['news_id']])->decrement('bad_count');
                //删除利空记录
                $res = $model->where(['id' => $bad_info['id']])->delete();
            }
        } elseif ($params['good_bad_action_type'] == 'not_good') {//取消利好
            //判断是否有利好
            if (empty($good_info)) {
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '此利好不存在');
            }
            //资讯减少利好量
            News::where(['id' => $params['news_id']])->decrement('good_count');
            //删除利好记录
            $res = $model->where(['id' => $good_info['id']])->delete();
        } elseif ($params['good_bad_action_type'] == 'bad') {//利空
            //判断是否重复利空
            if (!empty($bad_info)) {
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '重复利空操作');
            }
            //资讯增加利空量
            News::where(['id' => $params['news_id']])->increment('bad_count');
            //增加利空记录
            $model->setUserId($request->uid);
            $model->setNewsId($params['news_id']);
            $model->setType('bad');
            $res = $model->save();
            //同步利好操作
            if (!empty($good_info)) {
                //资讯减少利好量
                News::where(['id' => $params['news_id']])->decrement('good_count');
                //删除利好记录
                $res = $model->where(['id' => $good_info['id']])->delete();
            }
        } elseif ($params['good_bad_action_type'] == 'not_bad') {//取消利空
            //判断是否有利空
            if (empty($bad_info)) {
                return MyQuit::returnMessage(MyCode::PARAM_ERROR, '此利空不存在');
            }
            //资讯减少利空量
            News::where(['id' => $params['news_id']])->decrement('bad_count');
            //删除利空记录
            $res = $model->where(['id' => $bad_info['id']])->delete();
        }
        if ($res) {
            return MyQuit::returnMessage(MyCode::SUCCESS, 'success');
        } else {
            return MyQuit::returnMessage(MyCode::SERVER_ERROR, 'server error');
        }
    }
}
