
# 说明
    本项目使用 hyperf 框架开发
    
# 框架部署

1. 通过 Composer 安装 hyperf/hyperf-skeleton 项目
    
    composer create-project hyperf/hyperf-skeleton
    
2. 进入安装好的 Hyperf 项目目录
    
    cd hyperf-skeleton
   
3. 启动 Hyperf
        
    php bin/hyperf.php start
   
# 本项目部署

1. 拉取项目
    
    git pull git@gitlab.jingzhuan.cn:xcx-back/customer-service-chat.git

2. .env 配置
   

      APP_NAME=chat
      APP_ENV=dev
      DB_DRIVER=mysql
      DB_HOST=172.20.0.1
      DB_PORT=3306
      DB_DATABASE=chat
      DB_USERNAME=root
      DB_PASSWORD=123456
      DB_CHARSET=utf8mb4
      DB_COLLATION=utf8mb4_unicode_ci
      DB_PREFIX=
   
      REDIS_HOST=172.20.0.1
      REDIS_AUTH=(null)
      REDIS_PORT=6379
      REDIS_DB=0


3. 包安装
    
    composer install
   
4. 执行数据迁移
   
    php bin/hyperf.php migrate
   
5. 运行项目
   
    php bin/hyperf.php start

       [INFO] WebSocket Server listening at 0.0.0.0:9502
       [INFO] HTTP Server listening at 0.0.0.0:9501


# websocket 聊天接口

## 1. 用户登录
    
输入:
    
    {
        "type":"login",
        "platform":1,
        "user_type":"customer",
        "platform_user_id":1094,
        "nickname":"nickname",
        "avatar":"http://avatar"
    }
    
输出:
        
        {
            "code":0,
            "msg":"login",
            "data":{
                "type":"login",
                "room_id":11,
                "talk_time_at":"2020-11-17 02:15:37",
                "userInfo":{
                    "id":1,
                    "platform":1,
                    "platform_user_id":1094,
                    "user_type":"customer",
                    "user_role":0,
                    "status":1,
                    "nickname":"nickname",
                    "avatar":"http://avatar",
                    "describe":"",
                    "staff_name":"",
                    "login_at":"2020-11-16 14:58:12",
                    "deleted_at":null,
                    "created_at":"2020-11-16 07:24:06",
                    "updated_at":"2020-11-16 14:58:12"
                }
            }
        }

## 2. 用户私聊

    {
        "type":"secret",
        "room_id":11,
        "platform":1,
        "user_type":"customer",
        "chat_user_id":1,
        "nickname":"nickname",
        "avatar":"http://avatar",
        "content":"haha 2020-10-2 10:15:26",
        "content_type":1,
        "receive_platform":1,
        "receive_user_type":"staff",
        "receive_chat_user_id":2,
        "receive_nickname":"导师",
        "receive_avatar":"http://导师"
    }
    


# 客服操作
## 1. 客服登录

输入:

    {
        "type":"login",
        "platform":1,
        "user_type":"staff",
        "platform_user_id":408734,
        "nickname":"导师",
        "avatar":"http://导师"
    }
    
输出:

    {
        "code":0,
        "msg":"login",
        "data":{
            "type":"login",
            "room_id":0,
            "mine":1,
            "talk_time_at":"2020-11-17 02:18:10",
            "userInfo":{
                "id":2,
                "platform":1,
                "platform_user_id":408734,
                "user_type":"staff",
                "user_role":0,
                "status":1,
                "nickname":"员工",
                "avatar":"",
                "describe":"",
                "staff_name":"",
                "login_at":"2020-11-16 14:58:58",
                "deleted_at":null,
                "created_at":"2020-11-16 14:35:16",
                "updated_at":"2020-11-16 14:58:58"
            }
        }
    }

## 2. 客服绑定
输入:
    
    {
        "type":"bind",
        "room_id":11,
        "platform":1,
        "user_type":"staff",
        "chat_user_id":2,
        "nickname":"导师",
        "avatar":"http://导师"
    }
    
输出:
    
    {
        "code":0,
        "msg":"绑定成功",
        "data":{
            "type":"bind"
        }
    }

## 3. 客服私聊

    {
        "type":"secret",
        "room_id":11,
        "platform":1,
        "user_type":"staff",
        "chat_user_id":2,
        "nickname":"导师",
        "avatar":"http://导师",
        "content":"hahha",
        "content_type":1,
        "receive_platform":1,
        "receive_user_type":"customer",
        "receive_chat_user_id":1,
        "receive_nickname":"receive_nickname",
        "receive_avatar":"http://avatar"
    }

## 接收消息
    
    {
        "type":"reply",
        "tips":"[nickname]: 发送消息",
        "content":"haha 2020-10-2 10:15:26",
        "content_type":1
    }

## 结束会话

输入
        
    {
        "type":"finish",
        "platform": 1,
        "room_id": 21
    }

输出:
    
    {
        "type":"finish",
        "platform": 1,
        "room_id": 21
        "finish": 1
    }

# 表设计
    
本项目可独立部署运行，不依赖其他任何项目，属于服务化应用，服务需求方仅需求遵循接口规则，即可快速接入实时客服IM聊天功能。

主体表设计共有三个表，1. chat_users 2. chat_conversations 3. chat_conversation_contents

1. chat_users 用户表
   
   用户信息表，仅记录用户简要信息，如平台类型，平台用户ID，昵称、头像、手机号、登录时间等。重点关注平台类型，和平台用户ID，以便于向对应平台反查用户详细信息。
   
   识别用户必须以 平台类型 + 平台用户ID 的组合形式，例如，平台类型为 1 表示商城用户，平台类型为 2 表示依蜜心选用户，两个不同平台的用户ID是会重复的。
   
   用户首次接入时，会直接创建用户信息并生成用户ID，即 chat_user.id，此 ID 是本项目关联的必要主键，如在 chat_conversations 中的 user_id 就是 chat_user.id 。

2. chat_conversations 房间表
   
   会话信息表，或者叫房间信息表，聊天应用中，分私聊和群聊，本项目设计理念是，无论私聊或群聊，都视为群聊，对话用户必须在同一个房间。信息转发是通过房间ID获取房间里的所有用户，然后进行转发。
   
   用户首次接入，都会创建一个房间，即在 chat_conversations 创建一条记录，此时 deal_user_id 为空，表示并没有任何客服接待用户，所以 is_deal=0, is_end=0 。is_reply 是用户和客服的共同使用状态，is_reply=0 表示用户发了消息给客服，但客服并未回复，is_reply=1 则反之。

   is_read 标记客服对信息的读取状态，customer_is_read 标记客记对信息的读取状态，一条针对用户，一条针对客服。
   
   
3. chat_conversation_contents 内容表
   
   此表记录了用户在什么房间，说了什么内容。


# 项目代码介绍

1. app/ 目录是项目代码
   
   1. app/Controller/ HTTP 接口
   2. app/Model/ 数据模型
   3. app/Repositories/ 数据仓库
   4. app/Services/ 服务
   5. app/Helpers/ 助手函数
   6. app/Job/ 异步任务
   7. app/Traits/ 公共方法
   8. app/WebsocketEvents/ websocket 设计重点
      
2. config/ 配置文件
      
   1. config/routes.php 路由文件
   2. config/autoload/server.php 进程启动配置
   
3. bin/ 脚本命令，如 php bin/hyperf.php start 
4. runtime/ 运行时缓存，可随意删除
5. migrations/ 迁移文件
6. test/ 测试用例
   

# websocket 介绍
   当执行 php bin/hyperf.php start 时，hyperf 会读取 config/autoload/server.php 文件，servers 声明了要启动的服务。websocket 的配置就在这里。 

   在 config/routes.php 有一项声明如下:

      // websocket
      Router::addServer('ws', function (){
         Router::get('/ws', 'App\Server\WebSocketController');
      });

   它表示让 hyperf 加载 App\Server\WebSocketController::class 这个类，并监听 三个事件，分别是 onHandShake、onMessage、onClose ，每当有对应类型的消息请求时，则进入对应的处理方法。

   当有对应类型进入时，socket 调度器 SocketDispatch::class 对这些不同类型进行了统一处理，不同的事件在使用前必须进行声明绑定，如: SocketDispatch::$bindingMap 当调度器适配到了对应类型，则实例化对应类型所绑定的类，进行实例化并将当前请求上下文需要的数据设置到对应的类当中。

   后续的设计和精髓在 app/WebsocketEvents 目录下，app/WebsocketEvents/Contract 是契约，app/WebsocketEvents/Impl 是契约的实现，*Event.php 是不同类型的运行实例。
   
