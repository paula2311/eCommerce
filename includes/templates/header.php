
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> <?php getTitle()?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $css ;?>bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $css ;?>frontend.css">
    
</head>
<body>
    <div class="upper-bar">
        <div class="container">
            <?php 
                if(isset($_SESSION['user'])){?>
                    <!-- Example single info button -->
                        <div class="btn-group my-info ">
                            <div class="group-one">
                                <?php
                                
                                $userImg = getAllFrom("*", "users", "UserID", "", "") ;
                                foreach($userImg  as $img){
                                    if ($_SESSION['user'] == $img['Username']) {
                                        echo '<img class="img-circle" src= "uploads/members-imgs/' . $img['avatar'] .'" alt="" />';
                                    }
                                }
                                ?>
                                <a href="profile.php" class="btn btn-outline-info">
                                    <?php echo $sessionUser?>
                                </a>
                            </div>
                            
                            <div class="info-bar">
                                <a class="item" href="newad.php">New Item</a>
                                <a class="item" href="profile.php#my-ads">My Items</a>
                                <a class="item" href="logout.php">Logout</a>
                                <a class="item" href="profile.php#Procurement"><i class="fa-solid fa-cart-plus"></i></a>
                            </div>
                        </div> 

                <?php
                    
                } else{
            ?>
            <a href="login.php" class="info-bar">
                <span class="item">Login / Signup </span>
            </a>
            <?php } ?>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light ">
    <a class="navbar-brand text-info" href="#">Brand</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#app-nav" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="app-nav">
        <ul class="navbar-nav ">
        <li class="nav-item active">
            <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
            <?php
            # if parent == 0 this mean that this category is main branch .. يعني ده فرع رئيسي هيبقي جواه اقسام مش قسم داخل فرع
                $allCats = getAllFrom("*", "categories", "Name", "where parent = 0", "", "ASC");
                foreach($allCats as $cat)
                //&pagename='. str_replace(' ','-',$cat['Name']) => دي معناها انه يحط داش بدل المسافة اللي ف اسم الكاتيجوري عشان كانت متبهدلة 
                    echo '<li> 
                            <a class="nav-link" href="categories.php?pageid='.$cat['ID'] . '">'
                                    . $cat['Name'].
                            '</a> 
                        </li>';
            ?>
        </li>
        </ul>
    
    </div>
    </nav>
