# Ltalk


- 前端项目地址：https://github.com/LTalkTeam/LTalkHTML

- 在线预览：http://118.24.77.25/LTalkHTML/login.html

- 联系方式：octopus.zy.cn@gmail.com


### 本项目v1版本有以下功能：
- [x] 登录 
- [x] 注册
- [x] 添加好友与处理好友请求
- [x] 好友列表
- [x] 好友即时聊天
- [x] 世界聊天模块（所有在线人员）
- [x] 创建群组
- [x] 群组列表
- [x] 加入群组
- [x] 群组及时聊天
- [x] 好友离线上线提醒
- [x] 统计功能（当前在线总人数，基于日志的流量统计等）

### v2 或以后版本会完善
- 删除好友
- 退出群组
- 离线接受好友请求
- 接收离线消息
- 查看历史聊天记录



## 目录结构

目录结构如下：

~~~
LTalk  WEB部署目录
├─app                     应用目录
│  ├─Exception            自定义异常
│  ├─HttpController       HttpApi 控制器目录
│  │  ├─Common.php        公共方法
│  │  └─Router.php        自定义路由
│  │
│  ├─Model                tp orm 
│  ├─Service              服务层
│  ├─Sock                 websocket 输入输出规范配置
│  ├─Task                 异步Task方法模块
│  ├─Utility              进程池
│  ├─Validate             自定义验证层模块
│  └─WebsocketController  Websocket Api 控制器目录
...
(其他为 easyswoole 框架自带)
~~~
