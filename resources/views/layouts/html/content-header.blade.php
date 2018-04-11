<h1>{{ $title }}</h1>
<ol class="breadcrumb">
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i> Home</a></li>
    @foreach($menu as $url => $data)
        @if(isset($data['last']))
            <li class="active">{{ $data['name'] }}</li>
        @else
            <li><a href="{{ url($url) }}"><i class="fa {{ $data['icon'] or '' }}"></i> {{ $data['name'] }}</a></li>
        @endif
    @endforeach
</ol>