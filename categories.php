<?php 
ob_start();
session_start();
$pageTitle = 'Categories';
include 'ini.php';  
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
            echo '<div class="alert alert-danger"><strong>You Must Login</strong> </div>';
            
            
        }
    }

?>
<!--*** start of categories page  -->

<div class="container">
    <h1 class="text-center">Categories</h1>
    <div class="row">
        <?php
        if (isset($_GET['pageid']) && is_numeric($_GET['pageid'])) {
            $allItems = getAllFrom("*","items", "Item_ID","WHERE Cat_ID={$_GET['pageid']}", "AND Approve = 1");
            foreach( $allItems as $item){
                $price = $item['Price'];
                $imge = $item['avatar'];
                $name = $item['Name'];
                $desc = $item['Description'];
                $date = $item['Add_Date'];
                echo '<div class="col-sm-6 col-lg-3">';   
                    echo '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
                        echo '<div class="thumbnail item-box">';
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
                    echo'</form>'; 
                echo '</div>';    
            }
        }else {
            echo '<div class="alert alert-danger container"> You Did not Specify Page ID </div>';
            header("Location: profile.php");
        }
            
        ?>
    </div>
</div>

<!--*** end of categories page  -->
<?php include $tpl.'footer.php';
ob_end_flush();
?>
