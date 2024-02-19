<?php
session_start();
$pageTitle = 'Edit Info';
    include 'ini.php';
    $do = isset($_GET['do']) ?$do = $_GET['do'] : $do = 'Manage';
    if (isset($_SESSION['user'])) {
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']):0;
        $getUser = $con->prepare("SELECT * FROM users WhERE UserID=? LIMIT 1");
        $getUser->execute(array($userid));
        $row = $getUser->fetch();
        
        $count = $getUser->rowCount(); // عدد الصفوف 
        if ($count > 0) {?> 
            <h1 class="text-center">Edit Member</h1>
            <div class="container">
                <form action="?do=Update" METHOD="POST" class="form-horizontal" enctype="multipart/form-data"> <!-- ?do=Update معناها حولني علي نفس الصفحة وجزء الابديت خاصة-->
                    <input type="hidden" name="userid" value="<?php echo $userid?>">
                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Username</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="username" class="form-control" value="<?php echo $row['Username']?>" autocomplete="off">
                        </div>
                    </div> 

                    <div class="form-group">
                        <label class="col-sm-12 control-label">Password</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="hidden" name="oldpassword" value="<?php echo $row['Password']?> "autocomplete="off">
                            <input type="password" name="newpassword" class="form-control" autocomplete="off" placeholder="Leave Blank If You Dont Want To Change">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-12 control-label">E-mail</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="email" name="email" class="form-control" value="<?php echo $row['Email']?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-12 control-label">Full Name</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="full" class="form-control" value="<?php echo $row['FullName']?>">
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="col-sm-2 control-label" >Image</label>
                        <div class="col-sm-10 col-md-9">
                            <input type="file" name="image" >
                        </div>
                    </div> 

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Save" class="btn btn-outline-primary">
                        </div>
                    </div>
                </form>
            </div>
        <?php }
        else{
            echo '<div class="container">';
                $message = '<div class="alert alert-danger">There Is No Such ID</div>';
                #redirectHome($message);
            echo '</div>';
        }

        if ($do == 'Update') { // update bage
            echo '<h1 class="text-center">Update Member</h1>';
            echo '<div class="container">';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $formErrors = array();
                        // get the variables from the form
                        $id     = $_POST['userid'];
                        $user   = $_POST['username'];
                        $email  = $_POST['email'];
                        $name   = $_POST['full'];
// password trick عملت اتنين انبوت للباس واحد قديم فاضي اللي ف الداتا بيز والتاني جديد
//* لو الجديد ده الخانة بتاعته فاضية يبقي اليوزر ماعدلتش وخليه الباس زي ماهو
//* يبقي غير الباس ف الداتا بيز للقيمة الجديدة
                        $pass   = empty($_POST['newpassword']) ? $pass = $_POST['oldpassword'] : $pass = sha1($_POST['newpassword']);
//#############################################################################//
            // upload variable 
            $avatarName = $_FILES["image"]["name"];
            $avatarSize = $_FILES["image"]["size"];
            $avatarTmp  = $_FILES["image"]["tmp_name"];
            $avatarType = $_FILES["image"]["type"];
            $avatarAllowedExtensions = array("jpeg", "jpg", "png", "gif"); 
            // get avatar extension
            $avatarExtension = strtolower(end( explode('.', $avatarName) ) );
            if (!empty($avatarName) && ! in_array($avatarExtension, $avatarAllowedExtensions)) {
                $formErrors[] = 'This Extension Is <strong>NOT Allowed!</strong>' ;
            }
            if ($avatarSize > 4194304) {
                $formErrors[] = 'Imge Cant Be Larger Than  <strong>4MB</strong>' ;
            }
            foreach($formErrors as $error)
            echo '<div class="alert alert-danger">'.$error.'</div>' ;
            // check if there is no errors proced the updates
            if(empty($formErrors)){
                $avatar = rand(0, 1000000000) . '_' . $avatarName;
                move_uploaded_file($avatarTmp, "uploads\members-imgs\\".$avatar);
            }
//#############################################################################//
                            if (strlen($user) < 4) {
                                $formErrors[] = 'Username Cant Be Less than <strong>4 Characters!</strong>' ;
                            }
                            if (empty($user)) {
                                $formErrors[] = 'Username Cant Be <strong>Empty!</strong>' ;
                            }
                            if (empty($email)) {
                                $formErrors[] = 'Email Cant Be <strong>Empty!</strong>' ;
                            }
                            if (empty($name)) {
                                $formErrors[] = 'Full Name Cant Be <strong>Empty!</strong>' ;
                            }
                            foreach($formErrors as $error){
                            echo '<div class="alert alert-danger">'.$error.'</div>' ;
                            }
                            // check if there is no errors proced the updates
                            if (empty($formErrors)) {
                                //لما تاجي تعمل ابديت  لاسم شخص يبقي لو لقيت غير اللي انت واقف عليه ماتحدث عشان ماينفعش اتنين بنفس الاسم
                                $stmt = $con->prepare("SELECT * FROM users WHERE Username=? AND UserID !=?");
                                $stmt->execute(array($user,$id));
                                $cout = $stmt->rowCount();
                                if($cout >0){
                                    echo '<div class="alert alert-danger"> This User Is Exist!</div>';
                                }
                                else{
                                    // update the database with this info
                                    $stmt = $con->prepare("UPDATE users  SET Username=?, Email=?, FullName=?, Password=?, avatar=? WHERE UserID=?");
                                    $stmt->execute(array( $user, $email, $name, $pass, $avatar, $id));
                                    
                                // echo success message
                                    $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Updated' . '</div>';
                                    // redirectHome($message,'back');
                                    # هخليه يعمل تسجيل دخول جديد عشان البيانات الجديدة تتعرض ف صفحة البروفايل لانها بتتعرض من خلال كل سيشن جديدة
                                    session_unset();
                                    session_destroy();
                                    header("Location: login.php");
                                    exit;                          
                                }
                            }
                            else {
                                $message= '<div class="alert alert-danger">Sorry, You Cant Browse This Bage Directly</div>';
                                redirectHome($message);
                            }
            echo'</div>'; 
        
                    
                    }
        }

    }

    
    else {
        header('Location: login.php');
        exit();
    }
    include $tpl.'footer.php';
?>