### <center>壁纸爬虫网站</center>

项目已经适配的壁纸网站
- [wallhaven](https://wallhaven.cc "wallhaven")
- [unsplash](https://unsplash.com "unsplash")

项目地址：https://github.com/Juenfy/wallpaper

```shell
git clone https://github.com/Juenfy/wallpaper.git

cd wallpaper

composer install
```
项目里有两个数据库文件，自己导入，.env里做好配置
这是爬的壁纸数据，是按源壁纸网站各分类分页爬虫的，数据不会重复
![laravel+queryList爬虫各大壁纸网站，打造自己的壁纸网站](https://cdn.learnku.com/uploads/images/202109/22/82399/ktJ5bv6TZF.png!large)

项目技术栈：

1. laravel队列，爬虫任务都是丢到队列里跑的，延时执行，避免频繁请求，爬出来的壁纸数据会持久化到数据库中。
   执行下面命令监听消费队列：
```shell
php artisan queue:listen --queue=default --timeout=0
```
![](https://cdn.learnku.com/uploads/images/202109/22/82399/jbg53NgpB4.jpg!large)

2. 定时任务配合totem管理面板去管理定时任务。
   访问totem：自己项目域名/totem，比如我的就是http://wallpaper.com/totem
   ![laravel+queryList爬虫各大壁纸网站，打造自己的壁纸网站](https://cdn.learnku.com/uploads/images/202109/22/82399/sRklikLlyt.png!large)


3. QueryList，一款基于phpspider二次开发的爬虫框架。

4. 后台直接用laravel-admin的，省事。
   访问后台：自己项目域名/admin，比如我的就是http://wallpaper.com/admin
   账号：admin 密码：admin

5. 随便写了一个壁纸的展示列表。
   访问：直接域名即可，比如我的就是http://wallpaper.com
   瀑布流加载，但加载下一页动画效果有bug，会重叠
   ![laravel+queryList爬虫各大壁纸网站，打造自己的壁纸网站](https://cdn.learnku.com/uploads/images/202109/22/82399/H3FVLv9BaK.png!large)
