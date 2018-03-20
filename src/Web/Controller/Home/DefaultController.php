<?php

namespace src\Web\Controller\Home;

use Controller;
use Request;
use Response;
use Cache;
use AsyncRedis;

//请继承Controller
class DefaultController extends Controller
{
    //一个action 与route对应
    public function indexAction(Request $request)
    {   
        // for ($i=0; $i < 1555; $i++) { 
        //     yield \AsyncFile::write(__ROOT__."url-go.txt", "http://127.0.0.1:9876/seckill?userId={$i}\n", FILE_APPEND);
        // }
        
        yield "hello world!";
    }

    public function seckillAction(Request $request, $userId)
    {   
        $stime = "2018-03-20 13:52:50";
        if (date('Y-m-d H:i:s') <= $stime) yield new Response('秒杀开始时间为'.$stime, 200, [
            'Content-Type' => "text/html; charset=UTF-8"
        ]);
        //秒杀的商品数量为10件
        $goodsCount = 10;

        if (app('redisPool')->getFreePoolCount() == 0) yield new Response('服务器开了小差', 200, [
            'Content-Type' => "text/html; charset=UTF-8"
        ]);

        $saleCount = (yield AsyncRedis::lLen('saleInfo'));
        //还有库存,这里不能过滤掉所有用户，有可能会有10+个人进if代码
        if (intval($saleCount) < $goodsCount) {
            //单用户锁,一个用户只能进一次队列
            $res = (yield AsyncRedis::set('user_lock'.$userId, 1, 'NX', 'EX', 600));
            if ($res) {
                yield AsyncRedis::rPush('saleInfo', $userId);
            }

            //这个才是最终判断 是否秒杀成功的条件
            $userIds = (yield AsyncRedis::lRange('saleInfo', 0, 10));
            if (in_array($userId, $userIds)) {
                yield new Response('恭喜你，秒杀成功！获得资格', 200, [
                    'Content-Type' => "text/html; charset=UTF-8"
                ]);
            } else {
                yield new Response('没抢到哦！再试试！', 200, [
                    'Content-Type' => "text/html; charset=UTF-8"
                ]);
            }
        }

        //这个才是最终判断 是否秒杀成功的条件
        $userIds = (yield AsyncRedis::lRange('saleInfo', 0, 10));
        if (in_array($userId, $userIds)) {
            yield new Response('秒杀成功！获得资格', 200, [
                'Content-Type' => "text/html; charset=UTF-8"
            ]);
        } else {
            yield new Response('秒杀结束了！', 200, [
                'Content-Type' => "text/html; charset=UTF-8"
            ]);
            // yield new Response('秒杀结束了！获得资格的用户如下：'.implode(",", $userIds), 200, [
            //     'Content-Type' => "text/html; charset=UTF-8"
            // ]);
        }
    }
}
