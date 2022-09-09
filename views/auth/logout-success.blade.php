@extends('hanoivip::layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default" id="welcome" >
                <div class="panel-heading">Chúng tôi nhớ bạn..hẹn gặp lại bạn sớm</div>
                <p>Chuyển về trang chủ sau 3 giây ..</p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById( 'welcome' ).scrollIntoView();   
setTimeout(function(){ window.location = "{{route('home')}}" }, 3000);
</script>
@endsection
