<?php
include 'connect.php';

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
    //inculde navbar on all pages expect the one with $noNavbar variable;
    if(! isset($noNavbar)) {
        include  $tpl.'navbar.php';
    }










?>

