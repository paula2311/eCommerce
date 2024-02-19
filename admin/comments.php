<?php
session_start();
$pageTitle = 'Comments';
    if(isset($_SESSION['Username'])){

        include 'ini.php';
        /** start content of page here *******************************/
        $do = isset($_GET['do']) ?$do = $_GET['do'] : $do = 'Manage';
        if ($do == 'Manage') { // manage comments bage 
            
            $stmt =$con->prepare("SELECT 
                                    comments.*, items.Name AS Item_Name, users.Username 
                                FROM 
                                    comments
                                INNER JOIN
                                    items
                                ON
                                    items.Item_ID = comments.item_id
                                INNER JOIN
                                    users
                                ON
                                    users.UserID = comments.user_id
                                ");
            $stmt->execute();
            // assign to variable
            $comments = $stmt->fetchAll();
            if(! empty($comments)){
        
        ?>
            <h1 class="text-center">Manage Comments</h1>
            <div class="container">
                <div class="table-responsive">
                    <table class="main-table text-center table table-bordered">
                        <tr>
                            <td>#ID</td>
                            <td>Comment</td>
                            <td>Item Name</td>
                            <td>User Name</td>
                            <td>Added Date</td>
                            <td>Control</td>
                        </tr>
                        <?php foreach($comments as $comment){
                            echo '<tr>';
                            echo "<td>" . $comment["C_ID"]   . "</td>";
                            echo "<td>" . $comment["Comments"] . "</td>";
                            echo "<td>" . $comment["Item_Name"]    . "</td>";
                            echo "<td>" . $comment["Username"] . "</td>";
                            echo "<td>" . $comment["Comment_Data"]     ."</td>";
                            echo "<td>  <a href='comments.php?do=Edit&comid=".$comment['C_ID']."' class='btn btn-outline-success'><i class='fa fa-edit'></i> Edit</a>
                                        <a href='comments.php?do=Delete&comid=".$comment['C_ID']."' class='btn btn-outline-danger confirm'><i class='fa fa-close'></i> Delete</a>" ; 
                                    if($comment['Status'] == 0){
                                        echo"<a href='comments.php?do=Approve&comid=".$comment['C_ID']."' class='btn btn-outline-info activate'><i class='fa fa-check'></i></i> Approve</a>" ; 
                                    }
                            echo  "</td>";
                            echo '</tr>';
                        } 
                        ?>
                    </table>
                </div>
            </div>
            <?php
            }else{
                    echo '<div class="container">';
                        echo '<div class="alert alert-info">There Is No Comments To Show</div>';
                    echo '</div>';    
                }
        ?>
    <?php }
            
        elseif ($do == 'Edit') { // edit bage
        // check if get request userid is numeric & get the integer value of it    
            $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']):0;
            // select all data depend on this id
            $stmt = $con->prepare("SELECT * From comments WHERE C_ID=? ");
            $stmt -> execute(array($comid));
            $row = $stmt->fetch(); // جلب البيانات
            $count = $stmt->rowCount(); // عدد الصفوف 
            if ($count > 0) {?> 
            <h1 class="text-center">Edit Comment</h1>
            <div class="container">
                <form action="?do=Update" METHOD="POST" class="form-horizontal"> <!-- ?do=Update معناها حولني علي نفس الصفحة وجزء الابديت خاصة-->
                    <input type="hidden" name="comid" value="<?php echo $comid?>">
                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Comment</label>
                        <div class="col-sm-10 col-md-4">
                            <textarea class="form-control" name="comment"><?php echo $row['Comments'];?></textarea>
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
            echo '<h1 class="text-center">Update Comment</h1>';
            echo '<div class="container">';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        // get the variables from the form
                        $comid     = $_POST['comid'];
                        $comment   = $_POST['comment'];
                            // update the database with this info
                                $stmt = $con->prepare("UPDATE comments SET Comments=? WHERE C_ID=?");
                                $stmt->execute(array( $comment, $comid));
                             // echo success message
                                $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Updated' . '</div>';
                                redirectHome($message,'back');
                    }
                    else {
                        $message= '<div class="alert alert-danger">Sorry, You Cant Browse This Bage Directly</div>';
                        redirectHome($message);
                    }
            echo'</div>'; 
        } elseif ($do == 'Delete') { // delete page
            echo '<h1 class="text-center">Delete Comment</h1>';
            echo '<div class="container">';
                $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']):0;
                // select all data depend on this id                
                $check = checkItem('C_ID', 'comments', $comid);
                if ($check > 0) {
                // delete from data base
                    $stmt=$con->prepare('DELETE FROM comments WHERE C_ID=:zid');
                    $stmt->bindParam(':zid',$comid); 
                    $stmt->execute();
                    $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Deleted' . '</div>';
                    redirectHome($message,'back');

                } else{
                    $message = '<div class="alert alert-danger">This ID Is Not Exist</div>';
                    redirectHome($message);
                }
            echo '</div>';
        }
        elseif($do == 'Approve'){//activate page
            echo '<h1 class="text-center">Approve Comment</h1>';
            echo '<div class="container">';
                $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']):0;
                // select all data depend on this id                
                $check = checkItem('C_ID', 'comments', $comid);
                if ($check > 0) {
                // delete from data base
                    $stmt=$con->prepare('UPDATE comments SET Status = 1 WHERE C_ID = ?');
                    $stmt->bindParam(':zid',$comid); // $userid ب zuser باعمل باند ف حالة الحذف معناها بالعامية اربطلي ال 
                    $stmt->execute(array($comid));
                    $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Approved' . '</div>';
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