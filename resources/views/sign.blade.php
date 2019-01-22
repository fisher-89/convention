<!doctype html>
<html>
<head>
    <title>年会签到</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('css/sign.css')}}" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="{{asset('js/app.js')}}"></script>
</head>

<body>
<section>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{url('/sign')}}" method="post">
        @csrf
        <div>
            <label for="">名字：</label>
            {{--<input type="text" name="name" required minlength="2" maxlength="10" placeholder="请输入你的真实名字"/>--}}
            <input type="text" name="name" placeholder="请输入你的真实名字"/>
        </div>
        <div>
            <label for="">手机：</label>
            {{--<input type="text" name="mobile" required minlength="1" maxlength="11" placeholder="请输入手机号">--}}
            <input type="text" name="mobile" placeholder="请输入手机号">
        </div>
        <input type="submit" value="提交">
    </form>
    {{--<input type="button" value="提交" onclick="submit()">--}}
</section>
</body>
<script>
    /*
    function submit() {
        var params = $('form').serialize();
        $.ajax({
            type: 'post',
            url: '/sign',
            data: params,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                console.log(data)
            },
            error: function (errors) {
                console.log(errors)
            }
        })
    }
    */
</script>
</html>