<?php
session_start();
$pageTitle = 'New Item';
    include 'ini.php';
    if (isset($_SESSION['user'])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $formErrors = array();
            $name       = strip_tags($_POST['name']); // بديل filter_var($_POST['name'],FILTER_SANITIZE_STRING) .. او بديل الاسترنج بشكل عام
            $desc       = strip_tags($_POST['description']);// بديل filter_var($_POST['name'],FILTER_SANITIZE_STRING) .. او بديل الاسترنج بشكل عام
            $price      = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
            $country    = strip_tags($_POST['country']);// بديل filter_var($_POST['name'],FILTER_SANITIZE_STRING) .. او بديل الاسترنج بشكل عام
            $status     = filter_var( $_POST['status'], FILTER_SANITIZE_NUMBER_INT);
            $category   = filter_var( $_POST['category'], FILTER_SANITIZE_NUMBER_INT);
            $tags       = strip_tags($_POST['tags']); // بديل filter_var($_POST['name'],FILTER_SANITIZE_STRING) .. او بديل الاسترنج بشكل عام
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
                move_uploaded_file($avatarTmp, "uploads\item-imgs\\".$avatar);
            }
//#############################################################################//
                if (strlen($name) <4) {
                    $formErrors [] = '<div class="alert alert-danger"> Item Name Must Be More Than <strong>4 Characters !</strong> </div>';
                }
                if (strlen($desc) <10) {
                    $formErrors [] = '<div class="alert alert-danger"> Item Description Must Be More Than <strong>10 Characters !</strong> </div>';
                }
                if (strlen($country) <2) {
                    $formErrors [] = '<div class="alert alert-danger"> Item Country Must Be More Than <strong>2 Characters !</strong> </div>';
                }
                if (empty($price)) {
                    $formErrors [] = '<div class="alert alert-danger"> Item Price Must Be <strong>NOT Empty!</strong> </div>';
                }
                if (empty($status)) {
                    $formErrors [] = '<div class="alert alert-danger"> Item Status Must Be <strong>NOT Empty!</strong> </div>';
                }
                if (empty($category)) {
                    $formErrors [] = '<div class="alert alert-danger"> Item Category Must Be <strong>NOT Empty!</strong> </div>';
                }
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
                            "zcat"      =>$category ,
                            "zmember"   =>$_SESSION['uid'] ,
                            "ztags"     =>$tags,
                            "zavatar"  => $avatar
                        ));        
                        if ($stmt) {
                            $successMsg = '<div><strong>Item Added</strong> </div>';
                        }        
                    
            }
        }
?>
<h1 class="text-center"> Create New Item</h1>
<div class="create-ad block">
    <div class="container">
        <div class="card border-info">
            <div class="card-header bg-info text-white font-weight-bold"> Create New Item </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form action="<?php echo $_SERVER['PHP_SELF']?>" METHOD="POST" class="form-horizontal" enctype="multipart/form-data"> <!-- ?do=Insert معناها حولني علي نفس الصفحة وجزء الابديت خاصة-->
                            <div class="form-group ">
                                <label class="col-sm-2 control-label" >Name</label>
                                <div class="col-sm-10 col-md-9">
                                    <input type="text" name="name" class="form-control live-name" required>
                                </div>
                            </div> 

                            <div class="form-group ">
                                <label class="col-sm-2 control-label" >Description</label>
                                <div class="col-sm-10 col-md-9">
                                    <input type="text" name="description" class="form-control live-desc" >
                                </div>
                            </div> 

                            <div class="form-group ">
                                <label class="col-sm-2 control-label" >Price</label>
                                <div class="col-sm-10 col-md-9">
                                    <input type="text" name="price" class="form-control live-price" required>
                                </div>
                            </div> 

                            <div class="form-group ">
                                <label class="col-sm-2 control-label" >Country</label>
                                <div class="col-sm-10 col-md-9">
                                    <input type="text" name="country" class="form-control" >
                                </div>
                            </div> 

                            <div class="form-group ">
                                <label class="col-sm-2 control-label" >Status</label>
                                <div class="col-sm-10 col-md-9">
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
                                <label class="col-sm-2 control-label" >Category</label>
                                <div class="col-sm-10 col-md-9">
                                    <select class="form-control" name="category" id="" required>
                                            <option value="0"> ... </option>
                                        <?php
                                            $cats = getAllFrom('*','categories','ID','','');
                                            foreach ($cats as $cat) { 
                                                echo '<option value="'.$cat['ID'].'"> ' . $cat['Name'] . ' </option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div> 

                            <div class="form-group ">
                                <label class="col-sm-2 control-label" >Tags</label>
                                <div class="col-sm-10 col-md-9">
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


                    <div class="col-md-4">
                        <div class="thumbnail item-box live-preview">
                            <span class="price-tag">0$</span> 
                            <img class="img-responsive" src="img.png" alt="" />
                            <div class="caption">
                                <h3 class="text-info">name</h3>
                                <p>description</p>
                            </div>
                        </div>
                    </div>
                </div>
                

                <!--start looping through errors-->
                <?php
                    if (!empty($formErrors)) {
                        foreach($formErrors as $error){
                            echo '<div>'. $error . '</div>';
                        }
                    }
                    if (isset($successMsg)) {
                        echo "<div class='alert alert-success'>". $successMsg . "</div>";
                    }
                ?>
                <!--end looping through errors-->

            </div>
        </div>
    </div>
</div>






<?php   
    } 
    else {
        header('Location: login.php');
        exit();
    }
    include $tpl.'footer.php';
?>