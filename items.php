<?php
session_start();
$pageTitle = 'Show Items';
    include 'ini.php';
    $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']):0;
    // select all data depend on this id
    $stmt = $con->prepare("SELECT items .*,
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
                            WHERE
                                Item_ID=?
                            AND 
                                Approve = 1");
    $stmt -> execute(array($itemid));
    $count = $stmt->rowCount();
    if($count > 0){
    $item = $stmt->fetch(); // جلب البيانات
    
?>
<h1 class="text-center"><?php echo $item['Name']?></h1>
<div class="container ">
    <div class="row">
        <div class="col-md-3 item-box">
            <?php 
                echo "<img class='img-responsive' src= 'uploads/item-imgs/" . $item['avatar'] ."'alt=''/>";
            ?>
            
        </div>
        <div class="col-md-9 item-info">
            <h2><?php echo $item['Name']?></h2>
            <p><?php echo $item['Description']?></p>
            <ul class="list-unstyled">
                <li>
                <i class="fa-regular fa-calendar-days text-info"></i>
                <span class="text-info"> Added Date </span>: <?php echo $item['Add_Date']?>
                </li>
                <li>
                    <i class="fa-solid fa-money-bill-1-wave text-info"></i>
                    <span class="text-info"> Price </span>: <?php echo $item['Price']?>
                </li>
                <li>
                    <i class="fa fa-building text-info"></i>
                    <span class="text-info"> Made in </span>: <?php echo $item['Country_Made']?>
                </li>
                <li>
                    <i class="fa fa-tags text-info"></i>
                    <span class="text-info"> Category </span>: <a href="categories.php?pageid=<?php echo $item['Cat_ID']?> "> <?php echo $item['category_name']?> </a>
                </li>
                <li>
                    <i class="fa fa-user text-info"></i>
                    <span class="text-info"> Added By </span>: <a href="profile.php"> <?php echo $item['Username']?> </a>
                </li>
                <li>
                    <i class="fa fa-user text-info"></i>
                    <span class="text-info">Tags </span>: 
                        <?php
                            $allTags = explode(',' , $item['tags']); # حولت كل استرنج مفصول ب كومة ل اراي 
                            foreach ($allTags as $tag) {
                                $tag = str_replace(' ','', $tag);
                                $lowerTag = strtolower($tag);
                                echo "<a href='tags.php?name={$lowerTag}'>". $tag . '</a> | ';
                            }
                        ?>
                </li>
            </ul>
            
        </div>
    </div>
    <hr class="bg-info">
    <?php if(isset($_SESSION['user'])){?>
    <!---------------------------start add comment--------------------------->
    <div class="row">
        <div class="col-md-3 offset-md-3">
            <div class="add-comment">
                <h3>Add Your Comment</h3>
                <!--لو خليت الاكشن بي اتش اي سيلف بس هتودي علي نفس الصفحة وماينفعش اروحلها دايركت لازم اكمل واحط اي دي-->
                <form action="<?php echo $_SERVER['PHP_SELF'].'?itemid='.$item['Item_ID']?>" method="POST">
                    <textarea name="comment" class ="form-control" required></textarea>
                    <input type="submit" value="Add Comment" class="btn btn-primary">
                </form>
                <?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $comment  = strip_tags($_POST['comment']);//هاطلع منه اي تاجة لاي كود برمجي عشان الاختراق
                        $userid   = $_SESSION['uid'];
                        $itemid   = $item['Item_ID'];
                        if (!empty($comment)) {
                            $stmt = $con->prepare("INSERT INTO
                                                            comments(Comments, Status, Comment_Data, item_id, user_id)
                                                            VALUES(:zcomments, 0, NOW(), :zitem, :zuser)");
                            $stmt -> execute(array(
                                "zcomments" => $comment,
                                "zitem"     => $itemid,
                                "zuser"     => $userid
                            )); 
                            if ($stmt) {
                                echo '<div class=" text-center alert alert-success"> Comment Added</div>';
                            }
                            $comment='';
                        }
                        else {
                            echo '<div class=" text-center alert alert-danger">Please Write a Comment</div>';
                        }
                    }
                ?>
            </div>
        </div>
    </div>
        <!---------------------------end add comment--------------------------->
        <?php } else{ 
            echo '<div class="alert alert-info text-center"> <a href="login.php"">Login OR Register</a> To Add a Comment </div>';
        }?>
    <hr class="bg-info">
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
                                    item_id = ?
                            AND 
                                    Status=1
                            ORDER BY
                                    C_ID DESC        
                            ");
                            
        $stmt->execute(array($item['Item_ID']));
        // assign to variable
        $comments = $stmt->fetchAll();
                            
    ?>
    <?php
            foreach($comments as $comment){ ?>
                <div class="comment-box">
                    <div class="row">
                        <div class="col-sm-2 text-center">
                            <img class="img-responsive" src="img.png" alt="" />
                            <?php echo $comment['Username']?>
                        </div>

                        <div class="col-sm-10">
                            <p class="lead"> <?php echo $comment['Comments']?></p>
                        </div>
                    </div>
                </div>
                <hr class="bg-info">
        <?php } ?>
    
</div>

<?php   
    }else {
        echo '<div class ="container">';
            echo '<div class="alert alert-danger">thers is no such id OR this item waiting approval </div>' ;
        echo '</div>' ;
    }
    
    include $tpl.'footer.php';
?>