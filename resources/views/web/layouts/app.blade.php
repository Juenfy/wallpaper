<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="/static/common/css/app.css?ts={!! time() !!}">
    @yield('style')
</head>
<body>
    <nav>
        <form class="nav-form">
            <select class="source-select">
                <option value="0" @if($selected_sid == 0) selected @endif>
                    all
                </option>
                @foreach($sources as $item)
                    <option value="{{$item->id}}" @if($selected_sid == $item->id) selected @endif>{{$item->name}}</option>
                @endforeach
            </select>
        </form>
    </nav>
    <div id="content">
        @yield('content')
    </div>

    <footer>

    </footer>
    @yield('script')
</body>
</html>
