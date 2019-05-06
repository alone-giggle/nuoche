<?php


Route::domain('m', function () {

    //用户扫码进入（1、用户第一次扫码进行车辆绑定，2、其他用户扫码让车主挪车）
    Route::rule('car/index/:id$','m/car/index');

    //车主绑定车辆
    Route::rule('car/bind/:id$','m/car/bind');

});

