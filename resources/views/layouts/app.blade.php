<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Kmedix Group @yield('title', '')</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="/plugins/adminlte/css/AdminLTE.min.css">
    <link rel="stylesheet" href="/plugins/iCheck/square/blue.css">
    <link rel="stylesheet" href="/plugins/datatables/dataTables.bootstrap.css">
    <link rel="stylesheet" href="/plugins/adminlte/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="/plugins/bootstrap-fileinput/css/fileinput.min.css">
    <link rel="stylesheet" href="/plugins/intl-tel-input/css/intlTelInput.css">
    <link rel="stylesheet" href="/plugins/datepicker/datepicker3.css">
    <link rel="stylesheet" href="/plugins/rateit.js/rateit.css">
    <link rel="stylesheet" href="/assets/css/style.css">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="/plugins/rateit.js/jquery.rateit.min.js"></script>
</head>
<body class="hold-transition @yield('body_class', ' skin-blue fixed sidebar-mini')">
@if (!Auth::guest())
<div class="wrapper">
    <header class="main-header">
        <a href="/" class="logo">
            <span class="logo-mini">KG</span>
            <span class="logo-lg"><img src="/assets/img/logo.png" style="margin-top: -13px"/></span>
        </a>
        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="hidden-xs">{{ Auth::user()->first_name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <p>{{ Auth::user()->full_name }} - {{ Auth::user()->role }}</p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="{{ url('/logout') }}" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu">
                <li class="header">MAIN NAVIGATION</li>
                <?php
                    $isAdmin = Entrust::hasRole('ADMINISTRATOR');
                    $isEngineer = Auth::user()->isEngineer();
                    $isStoreAdmin = Entrust::hasRole('STORE_ADMINISTRATOR');
                ?>
                @if($isAdmin)
                    <li>
                        <a href="{{ url('/users') }}">
                            <i class="fa fa-users"></i> <span>Users</span>
                        </a>
                    </li>
                @endif
                @if($isAdmin)
                    <li>
                        <a href="{{ url('/customers') }}">
                            <i class="fa fa-user-md"></i> <span>Customers</span>
                        </a>
                    </li>
                @endif
                @if($isAdmin)
                    <li>
                        <a href="{{ url('/suppliers') }}">
                            <i class="fa fa-truck"></i> <span>Suppliers</span>
                        </a>
                    </li>
                @endif
                @if($isAdmin || $isEngineer)
                <li>
                    <a href="{{ url('/devices') }}">
                        <i class="fa fa-cog"></i> <span>Devices</span>
                        <?php $devices = \App\Device::all()->filter(function ($device) { return \Carbon\Carbon::parse($device->install_date)->addMonth($device->warranty) < \Carbon\Carbon::now(); }); ?>
                        @if($devices->count())<small class="label pull-right bg-red">{{ $devices->count() }}</small>@endif
                    </a>
                </li>
                @endif
                @if($isAdmin)
                    <li>
                        <a href="{{ url('/devicesModels') }}">
                            <i class="fa fa-tags"></i> <span>Devices models</span>
                        </a>
                    </li>
                @endif
                @if($isAdmin)
                <li>
                    <a href="{{ url('/serviceRequests') }}">
                        <i class="fa fa-fax"></i> <span>Service requests</span>
                        <?php $serviceRequests = \App\ServiceRequest::where('status', \App\ServiceRequest::REQUESTED)->get(); ?>
                        @if($serviceRequests->count())<small class="label pull-right bg-red">{{ $serviceRequests->count() }}</small>@endif
                    </a>
                </li>
                @endif
                @if($isAdmin || $isEngineer)
                <li>
                    <a href="{{ url('/serviceLog') }}">
                        <i class="fa fa-folder-o"></i> <span>Service log</span>
                    </a>
                </li>
                @endif
                @if($isAdmin || $isStoreAdmin)
                <li>
                    <a href="{{ url('/salesRequests') }}">
                        <i class="fa fa-cart-plus"></i> <span>Sales requests</span>
                        <?php $salesRequests = \App\SalesRequest::where('status', \App\SalesRequest::NOT_PROCESSED)->get(); ?>
                        @if($salesRequests->count())<small class="label pull-right bg-yellow">{{ $salesRequests->count() }}</small>@endif
                    </a>
                </li>
                @endif
                @if($isAdmin)
                <li>
                    <a href="{{ url('/complains') }}">
                        <i class="fa fa-frown-o"></i> <span>Complains</span>
                        <?php $complains = \App\Complain::where('status', \App\Complain::NOT_PROCESSED)->get(); ?>
                        @if($complains->count())<small class="label pull-right bg-yellow">{{ $complains->count() }}</small>@endif
                    </a>
                </li>
                @endif
                @if($isAdmin)
                <li>
                    <a href="{{ url('/salesRequest/1/notification') }}">
                        <i class="fa fa-mobile"></i> <span>Send Notification</span>
                    </a>
                </li>
                @endif
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header">
            @yield('content_header', '')
        </section>

        <!-- Main content -->
        <section class="content">
            @include('layouts.html.alert')
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!--<footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.3.3
        </div>
        <strong>Copyright &copy; 2016 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
        reserved.
    </footer>-->
</div>
@else
    @yield('content')
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="/plugins/fastclick/fastclick.js"></script>
<script src="/plugins/adminlte/js/app.min.js"></script>
<script src="/plugins/iCheck/icheck.min.js"></script>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="/plugins/bootstrap-fileinput/js/plugins/canvas-to-blob.min.js"></script>
<script src="/plugins/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
<script src="/plugins/bootstrap-fileinput/js/plugins/purify.min.js"></script>
<script src="/plugins/bootstrap-fileinput/js/fileinput.min.js"></script>
<script src="/plugins/bootstrap-fileinput/theme.js"></script>
<script src="/plugins/intl-tel-input/js/intlTelInput.js"></script>
<script src="/assets/js/scripts.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>
