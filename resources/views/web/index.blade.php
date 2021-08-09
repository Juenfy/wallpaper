@extends('web.layouts.app')

@section('title', '壁纸首页')

@section('style')
    <style>
        /* mq */

        /*@media (max-width: 600px) {
            .grid-item {
                width: 120px;
            }
        }*/

        .grid {
            width: 90%;
            margin: auto;
            position: relative;
        }

        .grid-item {
            float: left;
            padding: 10px;
            border: 1px solid #ccc;
            background: #f7f7f7;
            box-shadow: 0 0 8px #ccc;
        }

        .grid-item:hover {
            box-shadow: 0 0 10px #999;
        }

        .grid-item img {
            width: 240px;
        }
    </style>
@endsection

@section('content')
    <div class="grid" id="demo">
    </div>
@endsection

@section('script')
    <script src="/static/plugins/js/jquery.waterfall.js"></script>
    <script src="/static/plugins/js/dynamics.js"></script>
    <script>
        $(function () {
            let page = 1;//默认第一页
            let total_page = 1;
            $(".grid").waterfall({
                itemClass: ".grid-item",
                minColCount: 2,
                spacingHeight: 30,
                spacingWidth: 30,
                resizeable: true,
                ajaxCallback: function (success, end) {
                    page++;
                    console.log(total_page);
                    if (page <= total_page) {
                        loadData(page);
                    }
                    success();
                    end();
                }
            });

            function loadData(page, refresh = false) {
                var html = '';
                $.get("{!! url('/getWallPaperList') !!}", {
                    page: page,
                    per_page: 20
                }, function (res) {
                    if (res.code == 0) {
                        total_page = res.data.total_page;
                        res.data.wallpapers.forEach(function (item) {
                            html += '<div class="grid-item"><img src="' + item.cover + '"></div>';
                        });
                        refresh ? $(".grid").html(html) : $(".grid").append(html);
                    }
                });
            }

            $(".source-select").change(function () {
                var source_id = $(this).val();
                $.post('{!! url('/changeSource') !!}', {
                    source_id: source_id,
                    _token: '{!! csrf_token() !!}'
                }, function (res) {
                    console.log(res);
                    if (res.code == 0) {
                        loadData(1, true);
                    }
                });
            });

            loadData(page);

        });
    </script>
@endsection
