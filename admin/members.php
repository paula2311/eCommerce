<?php
session_start();
$pageTitle = 'Members';
    if(isset($_SESSION['Username'])){
        include 'ini.php';
        /** start content of page here *******************************/
        $do = isset($_GET['do']) ?$do = $_GET['do'] : $do = 'Manage';
        if ($do == 'Manage') { // manage members bage 
            $query = ''; //هاستخدمه في عرض الاعضاء الغير مفعلين فقط
            if(isset($_GET['page']) && $_GET['page']=='Pending'){
                $query = 'AND RegStatus = 0';
            }
            // select all user except admin
            $stmt =$con->prepare("SELECT * FROM users WHERE GroupID != 1 $query ORDER BY UserID DESC");
            $stmt->execute();
            // assign to variable
            $rows = $stmt->fetchAll();
        
                if(!empty($rows)){
        ?>
                    <h1 class="text-center">Manage Members</h1>
                    <div class="container">
                        <div class="table-responsive">
                            <table class="main-table manage-members text-center table table-bordered">
                                <tr>
                                    <td>#ID</td>
                                    <td>Imge</td>
                                    <td>Username</td>
                                    <td>Email</td>
                                    <td>Full Name</td>
                                    <td>Registerd Date</td>
                                    <td>Control</td>
                                </tr>
                                <?php foreach($rows as $row){
                                    echo '<tr>';
                                    echo "<td>" . $row["UserID"]   . "</td>";
                                    echo "<td>";
                                    if(empty($row["avatar"])) 
                                    echo "<img src= 'uploads/avatars/img.png' />" ;
                                    else
                                        echo "<img src= 'uploads/avatars/" . $row["avatar"] ."'alt=''/>";
                                    echo" </td>";
                                    echo "<td>" . $row["Username"] . "</td>";
                                    echo "<td>" . $row["Email"]    . "</td>";
                                    echo "<td>" . $row["FullName"] . "</td>";
                                    echo "<td>" . $row["Date"]     ."</td>";
                                    echo "<td>  <a href='members.php?do=Edit&userid=".$row['UserID']."' class='btn btn-outline-success'><i class='fa fa-edit'></i> Edit</a>
                                                <a href='members.php?do=Delete&userid=".$row['UserID']."' class='btn btn-outline-danger confirm'><i class='fa fa-close'></i> Delete</a>" ; 
                                            if($row['RegStatus'] == 0){
                                                echo"<a href='members.php?do=Activate&userid=".$row['UserID']."' class='btn btn-outline-info activate'><i class='fa fa-check'></i></i> Activate</a>" ; 
                                            }
                                    echo  "</td>";
                                    echo '</tr>';
                                } 
                                ?>
                            </table>
                        </div>
                        <a href="members.php?do=Add" class="btn btn-outline-primary"><i class="fa fa-plus"></i>New Member</a>
                    </div>
                <?php }
                else{
                    echo '<div class="container">';
                        echo '<div class="alert alert-info">There Is No Members To Show</div>';
                        echo '<a href="members.php?do=Add" class="btn btn-outline-primary"><i class="fa fa-plus"></i>New Member</a>';
                    echo '</div>';    
                }
        ?>
    <?php }

            elseif ($do == 'Add') { ?>
            <h1 class="text-center">Add New Member</h1>
            <div class="container">
                <!--enctype="multipart/form-data" ده نوع تشفير تحطه ف الفورم لما تيجي تبعت فيها فايل -->
                <form action="?do=Insert" METHOD="POST" class="form-horizontal" enctype="multipart/form-data"> <!-- ?do=Insert معناها حولني علي نفس الصفحة وجزء الابديت خاصة-->
                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Username</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="username" class="form-control" autocomplete="off" >
                        </div>
                    </div> 

                    <div class="form-group">
                        <label class="col-sm-12 control-label">Password</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="password" name="password" class="form-control" autocomplete="off"  required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-12 control-label">E-mail</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-12 control-label">Full Name</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="full" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-12 control-label">User Imge</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="file" name="avatar" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Add Member" class="btn btn-outline-primary">
                        </div>
                    </div>
                </form>
            </div>
       <?php
        }
        elseif($do == 'Insert'){ // insert bage

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo '<h1 class="text-center">Insert Member</h1>';
            echo '<div class="container">';
    //#############################################################################//
            // upload variable 
            $avatarName = $_FILES["avatar"]["name"];
            $avatarSize = $_FILES["avatar"]["size"];
            $avatarTmp  = $_FILES["avatar"]["tmp_name"];
            $avatarType = $_FILES["avatar"]["type"];
            $avatarAllowedExtensions = array("jpeg", "jpg", "png", "gif"); # المعايير اللي انا احددها لرفع الصورة
            // get avatar extension
            $avatarExtension = strtolower(end( explode('.', $avatarName) ) );# اكسبلود هتحول الاسترنج ل عناصر مفصولة ب دوت ف اراي,, والايند بتجيب اخر عنصر ف الاراي 

    //#############################################################################//


            // get the variables from the form
            $user   = $_POST['username'];
            $pass   = $_POST['password'];//بيشيك علي ده
            $email  = $_POST['email'];
            $name   = $_POST['full'];
            $hashpass = sha1($_POST['password']); // بيضيف ده ف الداتا بيز 
            //هيعتبر الاسترنج الفاضي باسورد وهيشفره $pass ليه بيشيك علي الباس ويضيف المشفر؟ عشان لو شفرت 
            //validate the form
            $formErrors = array();
            if (strlen($user) < 4) {
                $formErrors[] = 'Username Cant Be Less than <strong>4 Characters!</strong>' ;
            }
            if (empty($user)) {
                $formErrors[] = 'Username Cant Be <strong>Empty!</strong>' ;
            }
            if (empty($pass)) {
                $formErrors[] = 'Password Cant Be <strong>Empty!</strong>' ;
            }
            if (empty($email)) {
                $formErrors[] = 'Email Cant Be <strong>Empty!</strong>' ;
            }
            if (empty($name)) {
                $formErrors[] = 'Full Name Cant Be <strong>Empty!</strong>' ;
            }
            if (empty($avatarName)) {
                $formErrors[] = 'Imge Is <strong>Required</strong>' ;
            }
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
                move_uploaded_file($avatarTmp, "uploads\avatars\\".$avatar);
                //check if user exist in data base
                $check = checkItem('Username','users',$user);
                if($check > 0){
                    $message= '<div class="alert alert-danger">Sorry, This User Is Exist!</div>';
                    redirectHome($message,'back');
                } else{
                    // insert user info into database 
                    //فيه كذا طريقة للانسيرت ف الداتا بيز استخدمنا مرة اللي فيها ؟ ودي طريقة تانية 
                        $stmt = $con->prepare("INSERT INTO users 
                                                (Username, Password, Email, FullName, RegStatus, Date, avatar)
                                                VALUES (:zuser, :zpass, :zmail, :zname, 1, now(), :zavatar)"
                                            );
                        $stmt->execute(array(
                        //  key => value
                            "zuser"    => $user ,
                            "zpass"    => $hashpass ,
                            "zmail"    => $email ,
                            "zname"    => $name ,
                            "zavatar"  => $avatar
                        ));                
                    // echo success message
                        $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Inserted' . '</div>';
                        redirectHome($message,'back');
                    }
            }
            
        }
            else {
                echo '<div class = "container">';
                    $message= '<div class="alert alert-danger">Sorry, You Cant Browse This Bage Directly</div>';
                    redirectHome($message);
                echo '</div>';
            }
        echo'</div>'   ; 
        }
        elseif ($do == 'Edit') { // edit bage
        // check if get request userid is numeric & get the integer value of it    
            $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']):0;
            // select all data depend on this id
            $stmt = $con->prepare("SELECT * From users WhERE UserID=? LIMIT 1");
            $stmt -> execute(array($userid));
            $row = $stmt->fetch(); // جلب البيانات
            $count = $stmt->rowCount(); // عدد الصفوف 
            if ($count > 0) {?> 
            <h1 class="text-center">Edit Member</h1>
            <div class="container">
                <form action="?do=Update" METHOD="POST" class="form-horizontal"> <!-- ?do=Update معناها حولني علي نفس الصفحة وجزء الابديت خاصة-->
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
            redirectHome($message);
        echo '</div>';
   }
        }
        elseif ($do == 'Update') { // update bage
            echo '<h1 class="text-center">Update Member</h1>';
            echo '<div class="container">';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            // get the variables from the form
                                $id     = $_POST['userid'];
                                $user   = $_POST['username'];
                                $email  = $_POST['email'];
                                $name   = $_POST['full'];
                            /* password trick عملت اتنين انبوت للباس واحد قديم فاضي اللي ف الداتا بيز والتاني جديد
                            * لو الجديد ده الخانة بتاعته فاضية يبقي اليوزر ماعدلتش وخليه الباس زي ماهو
                            * يبقي غير الباس ف الداتا بيز للقيمة الجديدة
                            */
                                $pass   = empty($_POST['newpassword']) ? $pass = $_POST['oldpassword'] : $pass = sha1($_POST['newpassword']);

                            //validate the form
                            $formErrors = array();
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
                                    $stmt = $con->prepare("UPDATE users SET Username=?, Email=?, FullName=?, Password=? WHERE UserID=?");
                                    $stmt->execute(array( $user, $email, $name,$pass, $id));
                                // echo success message
                                    $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Updated' . '</div>';
                                    redirectHome($message,'back');
                                }
                            }
                            else {
                                $message= '<div class="alert alert-danger">Sorry, You Cant Browse This Bage Directly</div>';
                                redirectHome($message);
                            }
            echo'</div>'; 
        
                    
        }
        } elseif ($do == 'Delete') { // delete page
            echo '<h1 class="text-center">Delete Member</h1>';
            echo '<div class="container">';
                $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']):0;
                // select all data depend on this id                
                $check = checkItem('userid', 'users', $userid);
                if ($check > 0) {
                // delete from data base
                    $stmt=$con->prepare('DELETE FROM users WHERE UserID=:zuser');
                    $stmt->bindParam(':zuser',$userid); // $userid ب zuser باعمل باند ف حالة الحذف معناها بالعامية اربطلي ال 
                    $stmt->execute();
                    $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Deleted' . '</div>';
                    redirectHome($message,'back');

                } else{
                    $message = '<div class="alert alert-danger">This ID Is Not Exist</div>';
                    redirectHome($message);
                }
            echo '</div>';
        }
        elseif($do == 'Activate'){//activate page
            echo '<h1 class="text-center">Activate Member</h1>';
            echo '<div class="container">';
                $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']):0;
                // select all data depend on this id                
                $check = checkItem('userid', 'users', $userid);
                if ($check > 0) {
                // delete from data base
                    $stmt=$con->prepare('UPDATE users SET RegStatus = 1 WHERE UserID = ?');
                    $stmt->bindParam(':zuser',$userid); // $userid ب zuser باعمل باند ف حالة الحذف معناها بالعامية اربطلي ال 
                    $stmt->execute(array($userid));
                    $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Activated' . '</div>';
                    redirectHome($message,'back');

                } else{
                    $message = '<div class="alert alert-danger">This ID Is Not Exist</div>';
                    redirectHome($message);
                }
            echo '</div>';
        }
   /** end content of page here ******************************/
        include $tpl.'footer.php';
    }
    else{
        header('Location: index.php');
        exit();
    }
    


?>