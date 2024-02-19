<?php
ob_start();
session_start();
    $pageTitle = 'Items';
    if(isset($_SESSION['Username'])){
        include 'ini.php';
        $do = isset($_GET['do']) ?$do = $_GET['do'] :  'Manage';
        if ($do == 'Manage') {
            $stmt =$con->prepare("SELECT items .*,
                                    categories.Name AS category_name, 
                                    users.Username 
                                FROM 
                                    items
                                INNER JOIN 
                                    categories 
                                ON 
                                    categories.ID = items.Cat_ID 
                                INNER JOIN
                                    users 
                                ON 
                                    users.UserID = items.Member_ID
                                ORDER BY Item_ID DESC   ");
            $stmt->execute();
            // assign to variable
            $items = $stmt->fetchAll();
        if(!empty($items)){
        
        ?>
            <h1 class="text-center">Manage Items</h1>
            <div class="container">
                <div class="table-responsive">
                    <table class="main-table text-center manage-members table table-bordered">
                        <tr>
                            <td>#ID</td>
                            <td>Image</td>
                            <td>Name</td>
                            <td>Description</td>
                            <td>Price</td>
                            <td>Adding Date</td>
                            <td>Category</td>
                            <td>Username</td>
                            <td>Control</td>
                        </tr>
                        <?php foreach($items as $item){
                            echo '<tr>';
                                echo "<td>" . $item["Item_ID"]           . "</td>";
                                echo "<td>";
                                    echo "<img src= '../uploads/item-imgs/" . $item["avatar"] ."'alt=''/>";
                                echo" </td>";    
                                echo "<td>" . $item["Name"]              . "</td>";
                                echo "<td>" . $item["Description"]       . "</td>";
                                echo "<td>" . $item["Price"]             . "</td>";
                                echo "<td>" . $item["Add_Date"]          ."</td>";
                                echo "<td>" . $item["category_name"]     ."</td>";
                                echo "<td>" . $item["Username"]          ."</td>";
                                echo "<td class='control'>  <a href='items.php?do=Edit&itemid=".$item['Item_ID']."' class='btn btn-outline-success'><i class='fa fa-edit'></i> Edit</a>
                                            <a href='items.php?do=Delete&itemid=".$item['Item_ID']."' class='btn btn-outline-danger confirm'><i class='fa fa-close'></i> Delete</a>" ; 
                                            if($item['Approve'] == 0){
                                                echo"<a href='items.php?do=Approve&itemid=".$item['Item_ID']."' class='btn btn-outline-info activate'><i class='fa fa-check'></i></i> Approve</a>" ; 
                                            }
                                echo  "</td>";
                            echo '</tr>';
                        } 
                        ?>
                    </table>
                </div>
                <a href="items.php?do=Add" class="btn btn-outline-primary"><i class="fa fa-plus"></i>New Item</a>
            </div>
            <?php }
                else{
                    echo '<div class="container">';
                        echo '<div class="alert alert-info">There Is No Items To Show</div>';
                        echo '<a href="items.php?do=Add" class="btn btn-outline-primary"><i class="fa fa-plus"></i>New Item</a>';
                    echo '</div>';    
                }
        ?>
        <?php  }
        elseif ($do == 'Add') {?>
            <h1 class="text-center">Add New Item</h1>
            <div class="container">
                <form action="?do=Insert" METHOD="POST" class="form-horizontal" enctype="multipart/form-data"> <!-- ?do=Insert معناها حولني علي نفس الصفحة وجزء الابديت خاصة-->
                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Name</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Description</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="description" class="form-control" >
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Price</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="price" class="form-control" required>
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Country</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="country" class="form-control" required>
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Status</label>
                        <div class="col-sm-10 col-md-4">
                            <select class="form-control" name="status" id="" required>
                                <option value="0"> ... </option>
                                <option value="1"> New </option>
                                <option value="2"> Like New </option>
                                <option value="3"> Used </option>
                                <option value="4"> Old </option>
                            </select>
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Member</label>
                        <div class="col-sm-10 col-md-4">
                            <select class="form-control" name="member" id="" >
                                    <option value="0"> ... </option>
                                <?php
                                    $allMembers = getAllFrom('*', 'users', 'UserID', '', '');
                                    foreach ($allMembers as $user) { 
                                        echo '<option value="'.$user['UserID'].'"> ' . $user['Username'] . ' </option>';
                                        
                                    }
                                ?>
                            </select>
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Category</label>
                        <div class="col-sm-10 col-md-4">
                            <select class="form-control" name="category" id="" >
                                    <option value="0"> ... </option>
                                <?php
                                    $allCats = getAllFrom('*', 'categories', 'ID', 'where parent = 0', '');
                                    foreach ($allCats as $cat) { 
                                        echo '<option value="'.$cat['ID'].'"> ' . $cat['Name'] . ' </option>';
                                        $childCats = getAllFrom('*', 'categories', 'ID', "where parent = {$cat['ID']}", '');
                                        foreach($childCats as $child){
                                            echo '<option value="'.$child['ID'].'"> *** ' . $child['Name'] . ' child from => ' . $cat['Name']. ' </option>';
                                        }

                                    }
                                ?>
                            </select>
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Tags</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="tags" class="form-control" placeholder="Separate Tags With Comma (,)">
                        </div>
                    </div> 

                    <div class="form-group ">
                                <label class="col-sm-2 control-label" >Image</label>
                                <div class="col-sm-10 col-md-9">
                                    <input type="file" name="image" required>
                                </div>
                    </div> 

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Add Item" class="btn btn-outline-primary">
                        </div>
                    </div>
                </form>
            </div>
        <?php 
        }
        elseif ($do == 'Insert') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                echo '<h1 class="text-center">Insert Item</h1>';
                echo '<div class="container">';
                // get the variables from the form
                $name       = $_POST['name'];
                $desc       = $_POST['description'];
                $price      = $_POST['price'];
                $country    = $_POST['country'];
                $status     = $_POST['status']; 
                $member     = $_POST['member']; 
                $cat        = $_POST['category']; 
                $tags        = $_POST['tags']; 

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
                move_uploaded_file($avatarTmp, "..\uploads\item-imgs\\".$avatar);
            }
//#############################################################################//
                //validate the form
                $formErrors = array();
                if (empty($name)) {
                    $formErrors[] = 'Name Cant Be  <strong> Empty! </strong>' ;
                }
                if (empty($desc)) {
                    $formErrors[] = 'Description Cant Be <strong>Empty!</strong>' ;
                }
                if (empty($price)) {
                    $formErrors[] = 'Price Cant Be <strong>Empty!</strong>' ;
                }
                if (empty($country)) {
                    $formErrors[] = 'Country Cant Be <strong>Empty!</strong>' ;
                }
                if ($status == 0) {
                    $formErrors[] = 'You Must Choose The <strong>Status!</strong>' ;
                }
                if ($member == 0) {
                    $formErrors[] = 'You Must Choose The <strong>Member!</strong>' ;
                }
                if ($cat == 0) {
                    $formErrors[] = 'You Must Choose The <strong>Category!</strong>' ;
                }
                foreach($formErrors as $error)
                echo '<div class="alert alert-danger">'.$error.'</div>' ;
                // check if there is no errors proced the updates
                if(empty($formErrors)){
                        // insert user info into database 
                        //فيه كذا طريقة للانسيرت ف الداتا بيز استخدمنا مرة اللي فيها ؟ ودي طريقة تانية 
                            $stmt = $con->prepare("INSERT INTO items 
                                    (Name, Description, Price, Country_Made, Status, Add_Date, Cat_ID, Member_ID, tags, avatar)
                                    VALUES (:zname, :zdesc, :zprice, :zcountry, :zstatus, now(), :zcat, :zmember, :ztags, :zavatar)"
                                    );
                            $stmt->execute(array(
                            //  key => value
                                "zname"     =>$name ,
                                "zdesc"     =>$desc ,
                                "zprice"    =>$price ,
                                "zcountry"  =>$country , 
                                "zstatus"   =>$status ,
                                "zcat"      =>$cat ,
                                "zmember"   =>$member, 
                                "ztags"     =>$tags ,
                                "zavatar"  => $avatar
                            ));                
                        // echo success message
                            $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Inserted' . '</div>';
                            redirectHome($message,'back');
                }
                
            }
                else {
                    echo '<div class = "container">';
                        $message= '<div class="alert alert-danger">Sorry, You Cant Browse This Bage Directly</div>';
                        redirectHome($message,'back');
                    echo '</div>';
                }
            echo'</div>'   ; 
        }
        elseif ($do == 'Edit') {
            // check if get request itemid is numeric & get the integer value of it    
            $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']):0;
            // select all data depend on this id
            $stmt = $con->prepare("SELECT * From items WhERE Item_ID = ? ");
            $stmt -> execute(array($itemid));
            $item = $stmt->fetch(); // جلب البيانات
            $count = $stmt->rowCount(); // عدد الصفوف 
            if ($count > 0) {
                ?> 
                    <h1 class="text-center">Edit Item</h1>
            <div class="container">
                <form action="?do=Update" METHOD="POST" class="form-horizontal"> <!-- ?do=Insert معناها حولني علي نفس الصفحة وجزء الابديت خاصة-->
                    <input type="hidden" name="itemid" value="<?php echo $itemid?>">    
                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Name</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="name" class="form-control" required
                            value="<?php echo $item['Name']?>">
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Description</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="description" class="form-control"
                            value="<?php echo $item['Description']?>" >
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Price</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="price" class="form-control" required
                            value="<?php echo $item['Price']?>">
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Country</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="country" class="form-control" required
                            value="<?php echo $item['Country_Made']?>">
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Status</label>
                        <div class="col-sm-10 col-md-4">
                            <select class="form-control" name="status" id="" required
                            value="<?php echo $item['Name']?>">
                                <option value="1" <?php if($item['Status'] == 1) {echo 'selected';}?>> New </option>
                                <option value="2" <?php if($item['Status'] == 2) {echo 'selected';}?>> Like New </option>
                                <option value="3" <?php if($item['Status'] == 3) {echo 'selected';}?>> Used </option>
                                <option value="4" <?php if($item['Status'] == 4) {echo 'selected';}?>> Old </option>
                            </select>
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Member</label>
                        <div class="col-sm-10 col-md-4">
                            <select class="form-control" name="member" id="" >
                                <?php
                                    $stmt = $con -> prepare("SELECT * FROM users");
                                    $stmt->execute();
                                    $users = $stmt->fetchAll();
                                    foreach ($users as $user) { 
                                        echo ' <option value=" ' . $user['UserID'].' " '; 
                                        if($item['Member_ID'] == $user['UserID'] ) {echo 'selected';} 
                                        echo'>' . $user['Username'] . ' </option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Category</label>
                        <div class="col-sm-10 col-md-4">
                            <select class="form-control" name="category" id="" >
                                <?php
                                    $stmt = $con -> prepare("SELECT * FROM categories");
                                    $stmt->execute();
                                    $cats = $stmt->fetchAll();
                                    foreach ($cats as $cat) { 
                                        echo ' <option value=" '.$cat['ID'].' " ';
                                        if($item['Cat_ID'] == $cat['ID'] ) {echo 'selected';}
                                        echo'>' . $cat['Name'] . ' </option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div> 

                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Tags</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="tags" class="form-control" placeholder="Separate Tags With Comma (,)"
                            value="<?php echo $item['tags']?>">
                        </div>
                    </div> 
                    
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Save Item" class="btn btn-outline-primary">
                        </div>
                    </div>
                </form>
                <!-- ------------------------------------------------------------------------------- -->
                    <?php
                        $stmt =$con->prepare("SELECT 
                        comments.*, users.Username 
                    FROM 
                        comments
                    INNER JOIN
                        users
                    ON
                        users.UserID = comments.user_id 
                    WHERE
                        item_id=? ");
                            $stmt->execute(array($itemid));
                            // assign to variable
                            $rows = $stmt->fetchAll();
                            if(!empty($rows)){

                            ?>
                            <h1 class="text-center">Manage [ <?php echo $item['Name']?> ] Comments</h1>
                                <div class="table-responsive">
                                    <table class="main-table text-center table table-bordered">
                                        <tr>
                                            <td>Comment</td>
                                            <td>User Name</td>
                                            <td>Added Date</td>
                                            <td>Control</td>
                                        </tr>
                                        <?php foreach($rows as $row){
                                            echo '<tr>';
                                            echo "<td>" . $row["Comments"] . "</td>";
                                            echo "<td>" . $row["Username"] . "</td>";
                                            echo "<td>" . $row["Comment_Data"]     ."</td>";
                                            echo "<td>  <a href='comments.php?do=Edit&comid=".$row['C_ID']."' class='btn btn-outline-success'><i class='fa fa-edit'></i> Edit</a>
                                                        <a href='comments.php?do=Delete&comid=".$row['C_ID']."' class='btn btn-outline-danger confirm'><i class='fa fa-close'></i> Delete</a>" ; 
                                                    if($row['Status'] == 0){
                                                        echo"<a href='comments.php?do=Approve&comid=".$row['C_ID']."' class='btn btn-outline-info activate'><i class='fa fa-check'></i></i> Approve</a>" ; 
                                                    }
                                            echo  "</td>";
                                            echo '</tr>';
                                        } 
                                        ?>
                                    </table>
                                </div>

                <!--------------------------------------------------------------------------------------  -->
            </div>
                <?php 
                            }
        }
            else {
                echo '<div class="container">';
                    $message = '<div class="alert alert-danger">There Is No Such ID</div>';
                    redirectHome($message);
                echo '</div>';
            }
        }

        elseif ($do == 'Update') {
            echo '<h1 class="text-center">Update Item</h1>';
            echo '<div class="container">';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // get the variables from the form
                    $id         = $_POST['itemid'];
                    $name       = $_POST['name'];
                    $desc       = $_POST['description'];
                    $price      = $_POST['price'];
                    $country    = $_POST['country'];
                    $status     = $_POST['status']; 
                    $cat        = $_POST['category']; 
                    $member     = $_POST['member']; 
                    $tags       = $_POST['tags']; 
                    //validate the form
                    $formErrors = array();
                    if (empty($name)) {
                        $formErrors[] = 'Name Cant Be  <strong> Empty! </strong>' ;
                    }
                    if (empty($desc)) {
                        $formErrors[] = 'Description Cant Be <strong>Empty!</strong>' ;
                    }
                    if (empty($price)) {
                        $formErrors[] = 'Price Cant Be <strong>Empty!</strong>' ;
                    }
                    if (empty($country)) {
                        $formErrors[] = 'Country Cant Be <strong>Empty!</strong>' ;
                    }
                    if ($status == 0) {
                        $formErrors[] = 'You Must Choose The <strong>Status!</strong>' ;
                    }
                    if ($member == 0) {
                        $formErrors[] = 'You Must Choose The <strong>Member!</strong>' ;
                    }
                    if ($cat == 0) {
                        $formErrors[] = 'You Must Choose The <strong>Category!</strong>' ;
                    }
                    foreach($formErrors as $error)
                    echo '<div class="alert alert-danger">'.$error.'</div>' ;
                    // check if there is no errors proced the updates
                        if(empty($formErrors)){
                            // update the database with this info
                                $stmt = $con->prepare("UPDATE 
                                                            items 
                                                        SET 
                                                            Name = ?, 
                                                            Description = ?, 
                                                            Price = ?, 
                                                            Country_Made = ?,
                                                            Status = ?,
                                                            Cat_ID = ?,
                                                            Member_ID = ?,
                                                            tags      =?
                                                        WHERE 
                                                            Item_ID = ? ");

                                $stmt->execute(array( $name, $desc, $price, $country, $status, $cat, $member, $tags, $id));
                                // echo success message
                                $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Updated' . '</div>';
                                redirectHome($message);
                        }
                    
                    }
                    else {
                        $message= '<div class="alert alert-danger">Sorry, You Cant Browse This Bage Directly</div>';
                        redirectHome($message,'back');
                    }
            echo'</div>'; 
        }
        elseif ($do == 'Delete') {
            echo '<h1 class="text-center">Delete Item</h1>';
            echo '<div class="container">';
                $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']):0;
                // select all data depend on this id                
                $check = checkItem('Item_ID', 'items', $itemid);
                if ($check > 0) {
                // delete from data base
                    $stmt=$con->prepare('DELETE FROM items WHERE Item_ID = :zitem');
                    $stmt->bindParam(':zitem',$itemid); // $userid ب zuser باعمل باند ف حالة الحذف معناها بالعامية اربطلي ال 
                    $stmt->execute();
                    $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Deleted' . '</div>';
                    redirectHome($message,'back');

                } else{
                    $message = '<div class="alert alert-danger">This ID Is Not Exist</div>';
                    redirectHome($message);
                }
            echo '</div>';
        }
        elseif ($do == 'Approve') {
            echo '<h1 class="text-center">Approve Item</h1>';
            echo '<div class="container">';
                $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']):0;
                // select all data depend on this id                
                $check = checkItem('Item_ID', 'items', $itemid);
                if ($check > 0) {
                // delete from data base
                    $stmt=$con->prepare('UPDATE items SET Approve = 1 WHERE Item_ID = ?');
                    $stmt->bindParam(':zitem',$itemid); // $userid ب zuser باعمل باند ف حالة الحذف معناها بالعامية اربطلي ال 
                    $stmt->execute(array($itemid));
                    $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Activated' . '</div>';
                    redirectHome($message,'back');

                } else{
                    $message = '<div class="alert alert-danger">This ID Is Not Exist</div>';
                    redirectHome($message);
                }
            echo '</div>';
        }
        include $tpl.'footer.php';

    }
    else{
        header('Location: index.php');
        exit();
    }
ob_end_flush();
?>