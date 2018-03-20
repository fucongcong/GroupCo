package main

import (
	"flag"
	"fmt"
	"github.com/gomodule/redigo/redis"
	"log"
	"net/http"
	"time"
)

func newPool(addr string) *redis.Pool {
	return &redis.Pool{
		MaxIdle:     50,
		IdleTimeout: 240 * time.Second,
		Dial:        func() (redis.Conn, error) { return redis.Dial("tcp", addr) },
	}
}

var (
	pool        *redis.Pool
	redisServer = flag.String("redisServer", ":6379", "")
)

func main() {
	flag.Parse()
	pool = newPool(*redisServer)
	http.HandleFunc("/seckill", seckill)
	log.Fatal(http.ListenAndServe("localhost:9876", nil))
}

func seckill(w http.ResponseWriter, r *http.Request) {

	var ntime = "2018-03-20 13:56:05"
	if time.Now().Format("2006-01-02 15:04:05") < ntime {
		fmt.Fprint(w, time.Now().Format("2006-01-02 15:04:05")+"秒杀未开始:"+ntime)
		return
	}

	r.ParseForm()
	userId := r.Form.Get("userId")
	fmt.Fprintf(w, "Hello, %s", userId)

	if pool.ActiveCount() > 50 {
		fmt.Fprint(w, "服务器开了小差")
		return
	}

	conn := pool.Get()
	saleCount, err := redis.Int(conn.Do("llen", "saleinfo-go"))
	if err != nil {
		fmt.Fprint(w, "服务器500")
		defer conn.Close()
		return
	}

	if saleCount < 10 {
		_, err := redis.String(conn.Do("set", "userlock-go-"+userId, userId, "NX", "EX", 600))
		if err == nil {
			conn.Do("rpush", "saleinfo-go", userId)
		}

		res, err := redis.Values(conn.Do("lrange", "saleinfo-go", 0, 10))
		for _, v := range res {
			if string(v.([]byte)) == userId {
				fmt.Fprint(w, "秒杀成功！获得资格")
				defer conn.Close()
				return
			}
		}

		fmt.Fprint(w, "秒杀失败！再试试吧")
		defer conn.Close()
		return
	}

	res, err := redis.Values(conn.Do("lrange", "saleinfo-go", 0, 10))
	for _, v := range res {
		if string(v.([]byte)) == userId {
			fmt.Fprint(w, "秒杀成功！获得资格")
			defer conn.Close()
			return
		}
	}

	fmt.Fprint(w, "秒杀结束了！")
	defer conn.Close()
	return
}
