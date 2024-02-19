<?php
ob_start();
session_start();
$pageTitle = 'Categories';
    if (isset($_SESSION['Username'])) {
        include 'ini.php';
        $do = isset($_GET['do']) ?$do = $_GET['do'] :  'Manage';
        if ($do == 'Manage'){ // Manage page
            $sort = 'ASC';
            $sort_array = array('ASC','DESC');
            if ( isset( $_GET['sort'] ) && in_array( $_GET['sort'] , $sort_array) ) {
                $sort = $_GET['sort'] ;
            }
            $stmt2 = $con -> prepare("SELECT * FROM Categories WHERE parent = 0 ORDER BY Name $sort");
            $stmt2 -> execute();
            $cats = $stmt2 -> fetchAll();
            ?>
                <h1 class="text-center">Manage Categories</h1>
                <div class="container categories">
                    <div class="panel panel-default">
                        <div class="panel-heading"><i class="fa-solid fa-list-check"></i> Manage Categories
                            <div class="ordering">
                                Ordering: 
                                <a class="<?php if($sort == 'ASC') {echo 'active';}?>" href="?sort=ASC"><i class="fa-sharp fa-solid fa-sort-up"></i> ASC</a> |
                                <a class="<?php if($sort == 'DESC') {echo 'active';}?>" href="?sort=DESC"><i class="fa-sharp fa-solid fa-sort-down"></i> DESC</a>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php
                                foreach($cats as $cat){
                                    echo '<div class="cat">';
                                        echo '<div class="hidden-btns">';
                                            echo '<a href="categories.php?do=Edit&catid='.$cat['ID'].'" class="btn btn-outline-success"><i class="fa fa-edit"></i> Edit </a>';
                                            echo '<a href="categories.php?do=Delete&catid='.$cat['ID'].'" class="btn btn-outline-danger confirm"><i class="fa fa-close"></i> Delete </a>';
                                        echo '</div>';
                                        echo '<h3>' . $cat['Name'].'</h3>';
                                        echo '<p>' ;if ( $cat['Description']==''){echo 'This Category Has No Description';} else{echo $cat['Description'];} echo'</p>';
                                        if($cat['Visibility']==1) {echo'<span class="visible"><i class="fa fa-eye"></i> Hidden</span>';}
                                        if($cat['Allow_Comment']==1) {echo'<span class="comment"><i class="fa fa-close"></i> Comment Disabled</span>';}
                                        if($cat['Allow_Ads']==1) {echo'<span class="ads"><i class="fa fa-close"></i> Ads Disabled</span>';}
                                        // get child categories
                                        # if parent == 0 this mean that this category is main branch .. يعني ده فرع رئيسي هيبقي جواه اقسام مش قسم داخل فرع
                                        $childCats = getAllFrom("*", "categories", "Name", "where parent = {$cat['ID']} ", "", "ASC");
                                        if (! empty($childCats)) {
                                            echo '<h4 class="child-head"> Child Ctegoties </h4>';
                                            echo '<ul class="child-cats">';
                                                foreach($childCats as $c){
                                                    //&pagename='. str_replace(' ','-',$cat['Name']) => دي معناها انه يحط داش بدل المسافة اللي ف اسم الكاتيجوري عشان كانت متبهدلة 
                                                    //jquery عشان لما باعمل هوفر علي لينك التعديل بيظهر لينك الحذف بس اول ما اروح عليه يختفي فكان لازم الكلاس يبقي علي الاب  بص علي liحطيت كلاس تشيلد لينك علي  
                                                    echo '<li  class="child-link"> 
                                                            <a href="categories.php?do=Edit&catid='.$c['ID'].'" class="btn btn-outline-primary">'. $c['Name'].'</a>
                                                            <a href="categories.php?do=Delete&catid='.$c['ID'].'" class="btn btn-outline-danger confirm"> Delete </a>
                                                        </li>';
                                                }
                                            echo '</ul>';
                                        }  
                                    echo '</div>';
                                    echo '<hr>';
                                }
                            
                            ?>
                        </div>
                    </div>
                    <a href="categories.php?do=Add" class="btn btn-outline-primary add-cat"><i class="fa fa-plus"></i> Add Category</a>
                </div>
            <?php
        }
        elseif ($do == 'Add'){ ?> 
            <h1 class="text-center">Add New Category</h1>
            <div class="container">
                <form action="?do=Insert" METHOD="POST" class="form-horizontal"> <!-- ?do=Insert معناها حولني علي نفس الصفحة وجزء الابديت خاصة-->
                    <div class="form-group ">
                        <label class="col-sm-12 control-label" >Name</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="name" class="form-control" autocomplete="off" >
                        </div>
                    </div> 

                    <div class="form-group">
                        <label class="col-sm-12 control-label">Description</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="description" class="form-control" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-12 control-label">Ordering</label>
                        <div class="col-sm-10 col-md-4">
                            <input type="text" name="ordering" class="form-control">
                        </div>
                    </div>
            <!-----------------start category type ... is parent or child------------------>
                    <div class="form-group">
                        <label class="col-sm-12 control-label">Parent ?</label>
                        <div class="col-sm-10 col-md-4">
                            <select class="form-control" name="parent" >
                                <option value="0">None</option>
                                <?php
                                    # if parent == 0 this mean that this category is main branch .. يعني ده فرع رئيسي هيبقي جواه اقسام مش قسم داخل فرع
                                    $allCats =  getAllFrom("*", "categories", "ID", "WHERE parent = 0", "", "ASC");
                                    foreach($allCats as $cat){
                                        echo " <option value='".$cat['ID']."'>".$cat['Name']."</option> ";
                                    }
                                
                                ?>
                            </select>
                        </div>
                    </div>
            <!-----------------end category type ... is parent or child---------------------->
                    <div class="form-group">
                        <label class="col-sm-12 control-label">Visible</label>
                        <div class="col-sm-10 col-md-4">
                            <div>
                                <input id="vis-yes" type="radio" name="visibility" value="0" checked>
                                <label for="vis-yes">Yes</label>
                            </div>
                            <div>
                                <input id="vis-no" type="radio" name="visibility" value="1">
                                <label for="vis-no">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-12 control-label">Allow Commenting</label>
                        <div class="col-sm-10 col-md-4">
                            <div>
                                <input id="com-yes" type="radio" name="commenting" value="0" checked>
                                <label for="com-yes">Yes</label>
                            </div>
                            <div>
                                <input id="com-no" type="radio" name="commenting" value="1">
                                <label for="com-no">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-12 control-label">Allow Adds</label>
                        <div class="col-sm-10 col-md-4">
                            <div>
                                <input id="ads-yes" type="radio" name="ads" value="0" checked>
                                <label for="ads-yes">Yes</label>
                            </div>
                            <div>
                                <input id="ads-no" type="radio" name="ads" value="1">
                                <label for="ads-no">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" value="Add Category" class="btn btn-outline-primary">
                        </div>
                    </div>
                </form>
            </div>
        <?php }
        elseif ($do == 'Insert'){ // Insert page
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                echo '<h1 class="text-center">Insert Category</h1>';
                echo '<div class="container">';
                $name       = $_POST['name'];
                $desc       = $_POST['description'];
                $parent     = $_POST['parent'];
                $order      = $_POST['ordering'];
                $visible    = $_POST['visibility'];
                $comment    = $_POST['commenting']; 
                $ads        = $_POST['ads']; 
                    //check if category exist in data base
                    $check = checkItem('Name','categories',$name);
                    if($check > 0){
                        $message= '<div class="alert alert-danger">Sorry, This Category Is Exist!</div>';
                        redirectHome($message,'back');
                    } else{
                        // insert category info into database 
                        //فيه كذا طريقة للانسيرت ف الداتا بيز استخدمنا مرة اللي فيها ؟ ودي طريقة تانية 
                            # if parent == 0 this mean that this category is main branch .. يعني ده فرع رئيسي هيبقي جواه اقسام مش قسم داخل فرع
                            $stmt = $con->prepare("INSERT INTO categories
                                                    (Name, Description, parent,  Ordering, Visibility, Allow_Comment, Allow_Ads)
                                                    VALUES (:zname, :zdesc, :zparent, :zorder, :zvisible, :zcomment, :zads)"
                                                );
                            $stmt->execute(array(
                            //  key => value
                                "zname"     =>$name ,
                                "zdesc"     =>$desc ,
                                "zparent"   =>$parent ,
                                "zorder"    =>$order ,
                                "zvisible"  =>$visible ,
                                "zcomment"  =>$comment ,
                                "zads"      =>$ads 
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
        elseif ($do == 'Edit'){ // Edit page
             // check if get request catid  is numeric & get the integer value of it    
            $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']):0;
            // select all data depend on this id
            $stmt = $con->prepare("SELECT * From categories WhERE ID=? ");
            $stmt -> execute(array($catid));
            $cat = $stmt->fetch(); // جلب البيانات
            $count = $stmt->rowCount(); // عدد الصفوف 
            if ($count > 0) {
    ?>          
                <h1 class="text-center">Edit Category</h1>
                <div class="container">
                    <form action="?do=Update" METHOD="POST" class="form-horizontal"> <!-- ?do=Insert معناها حولني علي نفس الصفحة وجزء الابديت خاصة-->
                        <input type="hidden" name="catid" value="<?php echo $catid?>">
                        <div class="form-group ">
                            <label class="col-sm-12 control-label" >Name</label>
                            <div class="col-sm-10 col-md-4">
                                <input type="text" name="name" class="form-control" value="<?php echo $cat['Name'];?>" >
                            </div>
                        </div> 
                        <div class="form-group">
                            <label class="col-sm-12 control-label">Description</label>
                            <div class="col-sm-10 col-md-4">
                                <input type="text" name="description" class="form-control" value="<?php echo $cat['Description'];?>" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label">Ordering</label>
                            <div class="col-sm-10 col-md-4">
                                <input type="text" name="ordering" class="form-control" value="<?php echo $cat['Ordering'];?>" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label">Parent ?</label>
                            <div class="col-sm-10 col-md-4">
                                <select class="form-control" name="parent" >
                                    <option value="0">None</option>
                                    <?php
                                        # if parent == 0 this mean that this category is main branch .. يعني ده فرع رئيسي هيبقي جواه اقسام مش قسم داخل فرع
                                        $allCats =  getAllFrom("*", "categories", "ID", "WHERE parent = 0", "", "ASC");
                                        foreach($allCats as $c){
                                            echo " <option value='".$c['ID']."'";
                                                if($cat['parent'] == $c['ID']) {echo 'selected';}
                                            echo " >" .$c['Name']. "</option> ";
                                        }
                                    
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label">Visible</label>
                            <div class="col-sm-10 col-md-4">
                                <div>
                                    <input id="vis-yes" type="radio" name="visibility" value="0" <?php if($cat['Visibility'] == 0){echo 'checked';}?>>
                                    <label for="vis-yes">Yes</label>
                                </div>
                                <div>
                                    <input id="vis-no" type="radio" name="visibility" value="1" <?php if($cat['Visibility'] == 1){echo 'checked';}?>>
                                    <label for="vis-no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label">Allow Commenting</label>
                            <div class="col-sm-10 col-md-4">
                                <div>
                                    <input id="com-yes" type="radio" name="commenting" value="0" <?php if($cat['Allow_Comment'] == 0){echo 'checked';}?>>
                                    <label for="com-yes">Yes</label>
                                </div>
                                <div>
                                    <input id="com-no" type="radio" name="commenting" value="1" <?php if($cat['Allow_Comment'] == 1){echo 'checked';}?>>
                                    <label for="com-no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label">Allow Ads</label>
                            <div class="col-sm-10 col-md-4">
                                <div>
                                    <input id="ads-yes" type="radio" name="ads" value="0" <?php if($cat['Allow_Ads'] == 0){echo 'checked';}?>>
                                    <label for="ads-yes">Yes</label>
                                </div>
                                <div>
                                    <input id="ads-no" type="radio" name="ads" value="1" <?php if($cat['Allow_Ads'] == 1){echo 'checked';}?>>
                                    <label for="ads-no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="submit" value="Save" class="btn btn-outline-primary">
                            </div>
                        </div>
                    </form>
                </div>
                
    <?php   }
            else{
                        echo '<div class="container">';
                            $message = '<div class="alert alert-danger">There Is No Such ID</div>';
                            redirectHome($message);
                        echo '</div>';
                }
        }
        elseif ($do == 'Update'){//Update page
            echo '<h1 class="text-center">Update Category</h1>';
            echo '<div class="container">';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // get the variables from the form
                    $id         = $_POST['catid'];
                    $name       = $_POST['name'];
                    $desc       = $_POST['description'];
                    $order      = $_POST['ordering'];
                    $parent     = $_POST['parent'];
                    $visible    = $_POST['visibility'];
                    $comment    = $_POST['commenting']; 
                    $ads        = $_POST['ads']; 

                    
                        if(empty($formErrors)){
                            // update the database with this info
                                $stmt = $con->prepare("UPDATE categories
                                                        SET 
                                                            Name=?, 
                                                            Description=?, 
                                                            Ordering=?, 
                                                            parent=?, 
                                                            Visibility=?, 
                                                            Allow_Comment=?, 
                                                            Allow_Ads=? 
                                                        WHERE 
                                                            ID=?");
                                $stmt->execute(array( $name, $desc, $order, $parent, $visible,$comment,$ads, $id));
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
        elseif ($do == 'Delete'){ // Delete page
            echo '<h1 class="text-center">Delete Category</h1>';
            echo '<div class="container">';
                $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']):0;
                // select all data depend on this id                
                $check = checkItem('ID', 'categories', $catid);
                if ($check > 0) {
                // delete from data base
                    $stmt=$con->prepare('DELETE FROM categories WHERE ID=:zid');
                    $stmt->bindParam(':zid',$catid); 
                    $stmt->execute();
                    $message = '<div class = "alert alert-success">'. $stmt->rowCount() . ' '  . 'Record Deleted' . '</div>';
                    redirectHome($message,'back');

                } else{
                    $message = '<div class="alert alert-danger">This ID Is Not Exist</div>';
                    redirectHome($message);
                }
            echo '</div>';
        }
    }
ob_end_flush();
?>