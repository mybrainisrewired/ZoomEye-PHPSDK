# zoomeye-phpsdk
封装了zoomeye的php版本sdk,包括了
* 一个php版本的zoomeye的sdk
* 填完帐号密码即可开箱使用的示例代码
* 自动更新session
* 异常处理以及合理的错误代码显示



# 快速开始
``` .cfg
修改里面的用户名密码 同时把sessiontime设置为0 即可 
```

# SDK
getResourcesInfo
------------
获取当前帐号使用的权限

searchHost
------------
查询主机

searchWeb
------------
查询网站

getLastError
------------
最后一次执行的错误

# 注意事项 
需要使用curl
------------
 * 需要使用curl库
 * windows下开启方法：
 * 拷贝PHP目录中的libeay32.dll, ssleay32.dll, php5ts.dll, php_curl.dll文件到 system32 目录。(php_curl.dl在ext文件下)
 * 修改php.ini：配置好extension_dir，去掉extension = php_curl.dll前面的分号。
 * 重起apache或者IIS。
 * linux下开启方法：
 * 安装cURL
 * # wgethttp://curl.haxx.se/download/curl-7.17.1.tar.gz
 * # tar -zxf curl-7.17.1.tar.gz
 * # ./configure --prefix=/usr/local/curl
 * # make; make install
 * 安装php
 * 打开开关 --with-curl=/usr/local/curl
 
