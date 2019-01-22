<!doctype html>
<html>
<head>
    <title>年会签到</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
<section>
    <div>
        <img src="{{$avatar}}" alt="头像" title="头像">
        <p>{{$name}}</p>
        <p>{{$mobile}}</p>
    </div>
</section>
</body>
</html>