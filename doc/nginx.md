server {

    listen 80;
    root /var/www/Group-co;
    server_name groupco.com;

    location / {
        try_files $uri @rewriteapp;
    }
    location @rewriteapp {
        rewrite ^(.*)$ /index.php$1 last;
    }
    location ~ ^/(index)\.php(/|$) {
        proxy_set_header X-Real-IP  $remote_addr;
        proxy_set_header X-Forwarded-For $remote_addr;
        proxy_set_header Host $host;
        proxy_pass http://127.0.0.1:9777;
    }

    location ~* \.(jpg|jpeg|gif|png|ico|swf)$ {
        expires 3y;
        access_log off;
        gzip off;
    }

    # 配置css/js文件
    location ~* \.(css|js)$ {
        access_log off;
        expires 3y;
    }
}
