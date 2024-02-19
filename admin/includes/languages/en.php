<?php

function lang($phrase){
    static $lang = array(
        // dashboard page
        //navbar links
        'HOME_ADMIN'    => 'Admin',
        'Categories'    => 'Categories',
        'Items'         => 'Items',
        'Members'       => 'Members',
        'Comments'      => 'Comments',
        'Statistics'    => 'Statistics',
        'Logs'          => 'Logs',


    );
    return $lang[$phrase];
}




?>