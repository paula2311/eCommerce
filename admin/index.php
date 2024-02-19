<?php
    session_start();
     $noNavbar = ''; //ini.php اي صفحة مش عاوز فيها الناف بار حط الفاريابل ده فيها ورح بص علي صفحة
    $pageTitle = 'Login';
    if(isset($_SESSION['Username'])){
        header('Location: dashboard.php');
    }
    include 'ini.php';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['user'];
        $password = $_POST['pass'];
        $hashedPass = sha1($password); //md5() تشفير الباس زي 
        //PDO بس دي باستخدام ال mysql  دي نفس الطريقة العادية اللي بتحدد منها البيانات من 
        $stmt = $con->prepare("SELECT UserID, Username, Password 
                                From users 
                                WhERE Username=? 
                                AND Password =? 
                                AND GroupID=1 
                                LIMIT 1");

        $stmt -> execute(array($username, $hashedPass));
        $row = $stmt->fetch(); // جلب البيانات
        $count = $stmt->rowCount(); // عدد الصفوف 
        // if $count > 0 this mean the db contain record about this username
        if($count > 0){
            $_SESSION['Username'] = $username; // register session name
            $_SESSION['ID'] = $row['UserID']; // register session ID
            header('Location: dashboard.php');
            exit();
        }

    }
?>
    <!-- echo $_SERVER['PHP_SELF'] ==> دي بتبعت البيانات لنفس الصفحة-->
    <form class="login" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST"> 
        <h4 class="text-center">Admin Login</h4>
        <input class="form-control" type="text" name="user" placeholder="Username" autocomplete="off" >
        <input class="form-control" type="password" name="pass" placeholder="Password" autocomplete="new-password" >
        <input class="btn btn-primary btn-block" type="submit" value="Login">

    </form>

<?php
    include $tpl.'footer.php';
?>