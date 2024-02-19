<?php
session_start();
$pageTitle = 'Profile';
    if (isset($_SESSION['user'])) {
        include 'ini.php';
//////////////////////// ******************* orders ************************/////////////////////////
        if ($_SERVER["REQUEST_METHOD"] == "POST")  {
            if (isset($_SESSION['user'])) {
                $price      = $_POST['price'];
                $imge       = $_POST['imge'];
                $name       = $_POST['item-name'];
                $username   = $_SESSION['user'];
                $desc       = $_POST['desc'];
                $date       = $_POST['date'];
            
                    $stmt = $con->prepare("INSERT INTO orders 
                    (Price, Avatar, Item_Name, Username,  Description, Date)
                    VALUES (:zprice, :zavatar, :zname, :zuser, :zdesc, now())" );
                    $stmt->execute(array(
                    //  key => value
                        "zprice"    =>$price ,
                        "zavatar"   =>$imge,
                        "zname"     =>$name ,
                        "zuser"      =>$username ,
                        "zdesc"     =>$desc 
                    ));
                    if ($stmt) {
                        echo '<div class="alert alert-success"><strong>Item Added</strong> </div>';
                    }
                    else{
                        echo '<div class="alert alert-danger"><strong>Item NOT Added</strong> </div>';
                    }
            }
            else{
                header("Location: login.php");
                exit();
            }
        }
//////////////////////// ******************* orders ************************/////////////////////////
        $getUser = $con->prepare("SELECT * FROM users WHERE Username=?");
        $getUser->execute(array($sessionUser));
        $info = $getUser->fetch();
        $userid = $info['UserID'] ;
        
?>
<h1 class="text-center"> My Profile</h1>
<div class="info block">
    <div class="container">
        <div class="card border-info">
            <div class="card-header bg-info text-white font-weight-bold"> My Information </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li> 
                        <i class="fa fa-unlock-alt fa-fw text-info"></i>
                        <span class="text-info">Login Name</span> : <?php echo $info['Username'];?>
                    </li> 
                    <li> 
                    <i class="fa-regular fa-envelope text-info"></i>
                        <span class="text-info">Email</span> : <?php echo $info['Email'] ;?>
                    </li> 
                    <li> 
                        <i class="fa fa-user fa-fw text-info"></i>
                        <span class="text-info">Full Name</span> : <?php echo $info['FullName'];?>
                    </li> <br>
                </ul>
                <a href="<?php echo 'editMember.php?userid='.$userid.''?>" class="btn btn-outline-info">Edit Info</a>
            </div>
        </div>
    </div>
</div>

<div id="my-ads" class="my-ads block">
    <div class="container">
        <div class="card border-info">
            <div class="card-header bg-info text-white font-weight-bold"> Latest Adds </div>
            <div class="card-body">
                
                <?php
                    $myItems = getAllFrom("*", "items", "Item_ID", "where Member_ID = $userid", "") ;
                    if (!empty($myItems)) {
                        echo '<div class="row">';
                            foreach($myItems  as $item){
                                $price = $item['Price'];
                                $imge  = $item['avatar'];
                                $name  = $item['Name'];
                                $desc  = $item['Description'];
                                $date  = $item['Add_Date'];
                                echo '<div class="col-sm-6 col-lg-3">';
                                    echo '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
                                        echo '<div class="thumbnail item-box profile">';
                                            if ($item['Approve'] == 0) {
                                                echo '<span class="approve-status"> Not Approved !</span>';
                                            }
                                            echo '<span class="price-tag" name="price">$'.$price.'</span>';
                                            echo "<img class='img-responsive' name='imge' src= 'uploads/item-imgs/" . $imge ."'alt=''/>";
                                            echo '<div class="caption">';
                                            echo '<h3> <a name="item-name" href="items.php?itemid='.$item['Item_ID'].'">'.$name.'</a> </h3>';
                                            echo '<p name="desc">'.$desc.'</p>';
                                            echo '<input type="submit" value="Buy Now" class="btn btn-outline-info">';
                                            echo '<div  name="date" class="date">'.$date.'</div>';
                                            echo '</div>';
                                        echo '</div>';
                                        # hidden inputs ******************************************************************************
// اللي ف صورة او اسبان لازم يتبعت من انبوت ف باعمل انبوت مخفي واديله نفس النيم اللي انا عاوز ابعته والفاليو بتاعته كمان يعني الانبوت المخفي وسيلة ارسال بيانات من الفورم للداتا بيزname  ليه بعمل انبوت مخفي ؟ عشان ماينفعش ابعت ال 
                                        echo '<input type="hidden" name="price" value="'.$price.'" />';
                                        echo '<input type="hidden" name="imge" value="'.$imge.'" />';
                                        echo '<input type="hidden" name="item-name" value="'.$name.'" />';
                                        echo '<input type="hidden" name="desc" value="'.$desc.'" />';
                                        echo '<input type="hidden" name="date" value="'.$date.'" />';
                                # hidden inputs ******************************************************************************
                                    echo '</form>';
                                echo '</div>';    
                            }
                        echo '</div>';
                    }else{
                        echo '<div class="text-center alert alert-info">There Is No Adds, Create <a href="newad.php">New Ad</a></div>';
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<div id="Procurement" class="my-Procurement block">
    <div class="container">
        <div class="card border-info">
            <div class="card-header bg-info text-white font-weight-bold"> Procurement</div>
            <div class="card-body">
                <?php
                    $user_name = $_SESSION['user'];
                    $myItems = getAllFrom("*", "orders", "ID", "", "") ;
                    if (!empty($myItems)) {
                        echo '<div class="row">';
                            foreach($myItems  as $item){
                                $price = $item['Price'];
                                $imge = $item['Avatar'];
                                $name = $item['Item_Name'];
                                $desc = $item['Description'];
                                $date = $item['Date'];
                                if ($user_name == $item['Username']) {
                                    echo '<div class="col-sm-6 col-lg-3">';
                                        echo '<div class="thumbnail item-box profile">';
                                            echo '<span class="price-tag" name="price">$'.$price.'</span>';
                                            echo "<img class='img-responsive' name='imge' src= 'uploads/item-imgs/" . $imge ."'alt=''/>";
                                            echo '<div class="caption">';
                                            echo '<h3> <a name="item-name" href="#">'.$name.'</a> </h3>';
                                            echo '<p name="desc">'.$desc.'</p>';
                                            echo '<div  name="date" class="date">'.$date.'</div>';
                                            echo '</div>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                            }

                        echo '</div>';
                    }

                ?>
            </div>
        </div>
    </div>
</div>


<div class="my-comments block">
    <div class="container">
        <div class="card border-info">
            <div class="card-header bg-info text-white font-weight-bold"> Latest Comments </div>
            <div class="card-body">
                <?php
                    $myComments = getAllFrom("Comments", "comments", "C_ID", "where user_id=$userid", "") ;
                            if (!empty($myComments)) {
                                foreach($myComments as $comment){
                                    echo '<p class="text-dark">' .$comment['Comments'] .'</p>';
                                }
                            }
                            else {
                                echo '<div class="text-center alert alert-info">There Is No Comments </div>';
                            }
                ?>      
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