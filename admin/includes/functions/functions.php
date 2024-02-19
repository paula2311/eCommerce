<?php
# function to get all from any table
function getAllFrom($field, $table, $orderField, $where=null, $and = null, $ordering="DESC"){
    global $con;
    
    // البيانات هترجع بحسب ترتيب الحاجة المطلوبة بشكل تنازلي
    $getAll = $con -> prepare("SELECT $field FROM $table $where $and ORDER BY  $orderField $ordering ");
    $getAll -> execute();
    $all = $getAll -> fetchAll(); // كل البيانات اللي انا باحددها هترجع ف شكل اراي
    return $all;
}


function getTitle(){
    global $pageTitle ;
    if (isset($pageTitle)) {
        echo $pageTitle;
    }
    else {
        echo 'Default';
    }

}
/** Redirect function [...]
 * $message = echo error message
 * $seconds  = secondes before redirecting
 * $url
 */
function redirectHome( $message, $url=null, $seconds=3){
    if ($url === null) {
        $url = 'index.php';
        $link = 'Home Page';
    }
    else{
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '') {
            $url = $_SERVER['HTTP_REFERER']; //بيحولني للصفحة اللي قبلي 
            $link = 'Previous Page';
        }
        else{
            $url = 'index.php';
            $link = 'Home Page';
        }
        
    }
    echo $message ;
    echo "<div class='alert alert-info'>You Will Be Redirected To $link  After $seconds Seconds.</div>";
    header("Refresh: $seconds; url=$url");
    exit();
}
/** check items in database
 * $select
 * $from
 * $value
 * فانكشن للاختيار من الداتا بيز بشكل ديناميك اكتر يادوب هامررلها الارجيومينتس اللي عاوز اختارها فقط 
 * بدل ما اقعد اعمل سيليكت ف كل حتة يادوب اسم الفانكشن بس 
*/
function checkItem($select, $from, $value) {
    global $con;
    //علي طول مكان ؟ بس ده امان اكتر  $value واحط ال  array($value) اقدر اشيل ؟ وكمان جزء 
    $stmt2 = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
    $stmt2 -> execute(array($value));
    // عداد بيحسب هل فيه حاجه ف الداتا بيز ولا لا ..لو الكونت اكبر من 0 يبقي الحاجة دي موجودة
    $count=$stmt2->rowCount();
    return $count ;
    
}

/**
 * count number of items
 * $item = ex(UserID)
 * $table = ex(users)
 */
function countItems($itme, $table){
    global $con;
    $stmt3 = $con -> prepare("SELECT COUNT($itme) FROM $table");
    $stmt3 -> execute();
    return $stmt3->fetchColumn();
}

/**
 * get latest items from database
 * $order = ex(UserID or UserName ... etc)
 */
function getLatest($select, $table, $order, $limit){
    global $con;
    // البيانات هترجع بحسب ترتيب الحاجة المطلوبة بشكل تنازلي
    $stmt4 = $con -> prepare("SELECT $select FROM $table ORDER BY $order DESC LIMIT $limit");
    $stmt4 -> execute();
    $rows = $stmt4 -> fetchAll(); // كل البيانات اللي انا باحددها هترجع ف شكل اراي
    return $rows;
}


?>