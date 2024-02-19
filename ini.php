<?php
// Error Reporting بعمل ظهور لكل انواع الايرور اللي ممكن تقابلني عشان احلها 
ini_set('desplay_error','On');
error_reporting(E_ALL);

include 'admin/connect.php';

    $sessionUser = '';
    if (isset($_SESSION['user'])) {
        $sessionUser = $_SESSION['user'];
    }
    //routes 
    $tpl = 'includes/templates/';
    $lang = 'includes/languages/';
    $func = 'includes/functions/';
    $css = 'layout/css/';
    $js = 'layout/js/';
    

    // includes important files
    include $func.'functions.php';
    include  $lang.'en.php';
    include  $tpl.'header.php';
    










?>

