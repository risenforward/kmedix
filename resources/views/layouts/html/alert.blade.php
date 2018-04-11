@if ($alert = session('alert'))
    <div class="alert @if($alert['code'] == 200) alert-success @endif alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        @if($alert['code'] == 200)<strong>Well done!</strong>@endif {{ $alert['text'] }}
    </div>
@endif