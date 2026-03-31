<?php

Route::add('/', function () {
    // homepage is simply loading a static page
    // view the user routes for example following the MVC pattern
    require(__DIR__ . "/../views/pages/index.php");
});

require_once __DIR__ . "/user.php";
require_once __DIR__ . "/product.php";


