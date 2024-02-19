<?php
$do = isset($_GET['do']) ?$do = $_GET['do'] : $do = 'Manage';

// if the page is the main page 
if ($do == 'Manage') {
    echo 'Welcome You Are In The Manage Category Page';
    echo '<a href="page.php?do=Insert"> Add New Category + </a>';

}
elseif ($do == 'Add'){
    echo 'Welcome You Are In The Add Category Page';
}
elseif ($do == 'Insert'){
    echo 'Welcome You Are In The Insert Category Page';
}
else{
    echo 'Error: There Is No Page With This Name! ';
}







?>