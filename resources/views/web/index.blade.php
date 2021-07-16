@extends('web.layouts.app')

@section('title', '壁纸首页')

@section('style')
    <link rel="stylesheet" href="/static/plugins/layui/css/layui.css">
    <style>
        .grid {
            margin: 0 auto;
            position: relative;
            width: 90%;
        }

        .grid-item {
            position: absolute;
            /* fluffy */
            width: 300px;
            border-radius: 3px;
            background-color: #fff; /* end fluffy */
        }

        .grid-item > img {
            width: inherit;
            border-radius: 3px;
        }

        /* mq */

        @media (max-width: 600px) {
            .grid-item {
                width: 120px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="grid" id="lazy-load">

    </div>
@endsection

@section('script')
    <script src="/static/plugins/js/minigrid.js"></script>
    <script src="/static/plugins/js/dynamics.js"></script>
    <script src="/static/plugins/layui/layui.js"></script>
    <script>
        layui.use(['flow', 'jquery'], function () {
            var flow = layui.flow, $ = layui.jquery;
            var animate = function (item, x, y, index) {
                dynamics.animate(item, {
                    translateX: x,
                    translateY: y,
                    opacity: 1
                }, {
                    type: dynamics.linear,
                    duration: 100,
                    frequency: 120,
                    delay: index * 10
                });
            }

            var load = function () {
                flow.load({
                    elem: '#lazy-load' //流加载容器
                    //, scrollElem: '#lazy-load' //滚动条所在元素，一般不用填，此处只是演示需要。
                    , isAuto: true
                    , isLazyimg: true
                    , done: function (page, next) { //加载下一页
                        var list = [];
                        var time = new Date().getTime()
                        $.get('{!! url('/getWallPaperList') !!}', {
                            page: page,
                            per_page: 50
                        }, function (res) {
                            console.log(res);
                            time = new Date().getTime() - time;
                            if (res.code == 0) {
                                res.data.wallpapers.forEach((item) => {
                                    list.push('<div class="grid-item"><img lay-src="' + item.cover + '"></div>')
                                })
                                next(list.join(''), page < res.data.total_page); //假设总页数为
                                setTimeout(function () {
                                    minigrid('.grid', '.grid-item', 10, animate)
                                }, time);
                            }
                        });
                    }
                });
            }
            $(window).resize(function () {
                console.log(111);
                //minigrid('.grid', '.grid-item', 10, animate);
            });
            $(".source-select").change(function () {
                var source_id = $(this).val();
                $.post('{!! url('/changeSource') !!}', {
                    source_id: source_id,
                    _token: '{!! csrf_token() !!}'
                }, function (res) {
                    console.log(res);
                    if (res.code == 0) {
                        load();
                    }
                });
            });
            //load();
        })
    </script>
@endsection
