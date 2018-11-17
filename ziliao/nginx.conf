nginx跟目录定义在这个目录
nginx配置示例


server {
    listen                  80;
    server_name             wx.stink.zengzeng.com;

    set     $dir            /Users/zengzeng/Documents/www/wx_stink_service;
    set     $phpcgi         127.0.0.1:9000;
    set     $ci_env         development;
    set     $system_path    $dir/system;
    set     $app_path       $dir/application;


    root    $dir/;
    index   index.php index.html index.htm;


    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ ^\/(assets|static|resource)\/(v\_[0-9a-zA-Z-_.]+\/)?([^?#]*) {
        alias $app_path/assets/$3;
    }

    location ~ \.php($|/) {
        fastcgi_pass                $phpcgi;
        fastcgi_index               index.php;
        fastcgi_param               SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include                     fastcgi_params;
        fastcgi_param  CI_ENV       $ci_env;
        fastcgi_param  SYSTEM_PATH  $system_path;
        fastcgi_param  APP_PATH     $app_path;
    }

    location ~ /\.ht {
        deny  all;
    }
}