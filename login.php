<?php
ob_start();
    session_start();
    $pageTitle = 'Login';
    if(isset($_SESSION['user'])){
        header('Location: index.php');
}
    include 'ini.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['login'])) {
                $user = $_POST['username'];
                $pass = $_POST['password'];
            $hashedPass = sha1($pass); //md5() تشفير الباس زي 
            //PDO بس دي باستخدام ال mysql  دي نفس الطريقة العادية اللي بتحدد منها البيانات من 
                $stmt = $con->prepare("SELECT UserID, Username, Password From users WhERE Username=? AND Password =?");
                $stmt -> execute(array($user, $hashedPass));
                $get = $stmt->fetch();
            $count = $stmt->rowCount(); // عدد الصفوف 
            // if $count > 0 this mean the db contain record about this username
                if($count > 0){
                $_SESSION['user'] = $user; // register session name
                $_SESSION['uid']  = $get['UserID']; // register user id in session
                    header('Location: index.php');
                    exit();
            }else {
                $faildLogin =  ' Username Or Password Is Not Correct X ';
            }
        } else {# signup page
            $formErrors = array();
            $username = $_POST['username'] ;
            $password = $_POST['password'] ;
            $email    = $_POST['email']   ;

            //الفلتر الخاص ب اسم المستخدم
            if (isset($username)) {
                //هاعمل فلترة لليوزر عشان الامان ونوع الفلتر هو تعقيم لليوزر هيخليه استرنج
                $filterUser = strip_tags($username);//هاطلع منه اي تاجة لاي كود برمجي عشان الاختراق
                if (strlen($filterUser) < 4) {
                    $formErrors[] = 'Username Must Be More Than <strong> 4 Characters!</strong>';
                }
                
            }

            if (isset($_POST['password'])) {
                if (empty($_POST['password'])) {
                    $formErrors[] = 'Password Can Not <strong> Be Empty!</strong>';
                }
                elseif (strlen($_POST['password']) < 6) {
                    $pass = sha1($_POST['password']);
                    $formErrors[] = 'Password Must Contain <strong>Characters & Numbers </strong>And <strong>Be More Than 6!</strong>';
                }
            }

            // الفلتر الخاص ب  الايميل
            if (isset($email)) {
                //هاعمل فلترة للايميل  عشان الامان باعقمه
                $filterEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
                // هتاكد اذا كان صحيح او لا
                if (empty($filterEmail)) {
                    $formErrors[] = 'Email Can Not <strong> Be Empty!</strong>';
                }
                elseif (filter_var($filterEmail, FILTER_VALIDATE_EMAIL) != true) {
                    $formErrors[] = 'This Email is <strong> Not Valid!</strong>';
                }
                
            }
            
            // check if there is no errors proced the user add
            if(empty($formErrors)){
                //check if user exist in data base
                $check = checkItem('Username','users',$username);
                if($check > 0){
                    $formErrors[] = 'Sorry, This User Is Exist!';
                } else{
                    // insert user info into database 
                    //فيه كذا طريقة للانسيرت ف الداتا بيز استخدمنا مرة اللي فيها ؟ ودي طريقة تانية 
                        $stmt = $con->prepare("INSERT INTO users 
                                                (Username, Password, Email, RegStatus, Date)
                                                VALUES (:zuser, :zpass, :zmail, 0, now())"
                                            );
                        $stmt->execute(array(
                        //  key => value
                            "zuser"=>$username,
                            "zpass"=>sha1($password),
                            "zmail"=>$email 
                        ));                
                    // echo success message
                        $successMsg = 'Congrats, You Are Now Registerd User ';  
                }
            }

        }    

}
?>   
<!--######################################################################################-->
    <div class="container login-page">
        <h1 class="text-center"><span class="active" data-class="login">Login</span> | <span data-class="signup">Signup</span> </h1>
        <!-- echo $_SERVER['PHP_SELF'] ==> دي بتبعت البيانات لنفس الصفحة-->
        <form class="login" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
            <input class="form-control" name="username" type="text" placeholder="Username" autocomplete = "off" required>
            <input class="form-control" name="password" type="password" placeholder="Password" autocomplete = "new-password" required>
            <input name="login" class="btn btn-primary btn-block" type="submit" value="Login">
        </form>
        
        <form class="signup" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
            <input class="form-control" name="username" type="text" placeholder="username" autocomplete = "off" required>
            <input class="form-control" name="password" type="password" placeholder="Password" autocomplete = "new-password" minlength="6" required>
            <input class="form-control" name="email"    type="email" placeholder="E-mail Address" required>
            <input name="signup" class="btn btn-primary btn-block" type="submit" value="Signup">
        </form>
        <div class="the-errors text-center">
            <?php 
                if (!empty($formErrors)) {
                    foreach ($formErrors as $formError){
                        echo '<div class="alert alert-danger">'.$formError.'</div>';
                    }
                }
                if (isset($successMsg)) {
                    echo '<div class="alert alert-success">'.$successMsg.'</div>';
                }
                if (isset($faildLogin)) {
                    echo '<div class="alert alert-danger">'.$faildLogin.'</div>';
                }
            ?>
        </div>
    </div>
<!--######################################################################################-->


<?php
    include $tpl.'footer.php';
ob_end_flush();
?>