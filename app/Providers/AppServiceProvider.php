<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../vendor/almasaeed2010/adminlte/dist' => public_path('plugins/adminlte/'),
            __DIR__.'/../../vendor/almasaeed2010/adminlte/plugins/fastclick' => public_path('plugins/fastclick/'),
            __DIR__.'/../../vendor/almasaeed2010/adminlte/plugins/slimScroll' => public_path('plugins/slimScroll/'),
            __DIR__.'/../../vendor/almasaeed2010/adminlte/plugins/iCheck' => public_path('plugins/iCheck/'),
            __DIR__.'/../../vendor/almasaeed2010/adminlte/plugins/datatables' => public_path('plugins/datatables/'),
            __DIR__.'/../../vendor/almasaeed2010/adminlte/plugins/datepicker' => public_path('plugins/datepicker/'),
            __DIR__.'/../../vendor/kartik-v/bootstrap-fileinput/css/fileinput.min.css' => public_path('plugins/bootstrap-fileinput/css/fileinput.min.css'),
            __DIR__.'/../../vendor/kartik-v/bootstrap-fileinput/js/fileinput.min.js' => public_path('plugins/bootstrap-fileinput/js/fileinput.min.js'),
            __DIR__.'/../../vendor/kartik-v/bootstrap-fileinput/js/plugins' => public_path('plugins/bootstrap-fileinput/js/plugins'),
            __DIR__.'/../../vendor/kartik-v/bootstrap-fileinput/img' => public_path('plugins/bootstrap-fileinput/img'),
            __DIR__.'/../../vendor/kartik-v/bootstrap-fileinput/themes/fa/theme.js' => public_path('plugins/bootstrap-fileinput/theme.js'),
            __DIR__.'/../../vendor/bluefieldscom/intl-tel-input/build' => public_path('plugins/intl-tel-input'),
            __DIR__.'/../../vendor/bluefieldscom/intl-tel-input/lib/libphonenumber/build/utils.js' => public_path('plugins/intl-tel-input/utils.js'),
            __DIR__.'/../../vendor/gjunge/rateit.js/scripts' => public_path('plugins/rateit.js'),
        ], 'public');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
