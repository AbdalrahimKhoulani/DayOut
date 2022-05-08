<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('is_img',function($attribute, $value, $params, $validator) {
            $image = base64_decode($value);
            $f = finfo_open();
            $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
            if($result == 'image/png' || $result == 'image/jpeg' || $result == 'image/webp' || $result == 'image/x-ms-bmp')
                return true;
            return false ;
        });

        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();
    }
}
