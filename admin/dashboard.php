<?php
ob_start();
session_start();
    if(isset($_SESSION['Username'])){
        
        $pageTitle = 'Dashboard';
        include 'ini.php';
        /************************variables used bottom **************************/
            $numUsers = 4;
            $latestUsers = getLatest('*', 'users', 'UserID', $numUsers);
            $numItems = 4;
            $latestItems = getLatest('*', 'items', 'Item_ID', $numItems);
            $numComments = 4;
        /********************************************************************** */
        /** start dashboard here */
        ?>
        <div class="container home-stats text-center">
            <h1>Dashboard</h1>
            <div class="row">
                <div class="col-md-3">
                    <div class="stat st-members">
                        <i class="fa fa-users"></i>
                        <div class="info">
                            Total Members
                            <span><a href="members.php"><?php echo countItems('UserID','users');?></a></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat st-pending">
                        <i class="fa fa-user-plus"></i>
                        <div class="info">
                            Pending Members 
                            <span><a href="members.php?do=Manage&page=Pending">
                                <?php echo checkItem('RegStatus','users',0);?></a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat st-items">
                        <i class="fa fa-tag"></i>
                        <div class="info">
                            Total Items 
                            <span><a href="items.php"><?php echo countItems('Item_ID','items');?></a></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat st-comments">
                        <i class="fa fa-comments"></i>
                        <div class="info">
                            Total Comments 
                            <span>
                                <a href="comments.php"><?php echo countItems('C_ID','comments');?></a>
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="container latest">
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-users"></i> Latest <?php echo $numUsers?> Users
                        </div>
                        <div class="panel-body">
                            <ul class="list-unstyled latest-users">
                                <?php
                                    if(!empty($latestUsers)){
                                        foreach($latestUsers as $user){
                                            echo '<li>';
                                                echo $user['Username'];
                                                echo '<a href="members.php?do=Delete&userid='.$user['UserID'].'">';
                                                    echo '<span class="btn btn-outline-danger pull-right">';
                                                        echo '<i class="fa fa-close"></i> Delete';
                                                    echo '</span>'; 
                                                echo '</a>';
                                                echo '<a href="members.php?do=Edit&userid='.$user['UserID'].'">';    
                                                    echo '<span class="btn btn-outline-success pull-right">';
                                                        echo '<i class="fa fa-edit"></i> Edit'; 
                                                        if($user['RegStatus'] == 0){
                                                            echo"<a href='members.php?do=Activate&userid="
                                                            .$user['UserID']
                                                            ."' class='btn btn-outline-info activate pull-right'>
                                                            <i class='fa fa-check'></i> Activate</a>" ; 
                                                        }
                                                    echo '</span>';
                                                echo '</a>';
                                            echo '</li>';
                                        }
                                    }
                                    else{
                                        echo '<div class="alert alert-info">There Is No Members To Show </div>';
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-tag"></i> Latest <?php echo $numItems?> Items
                        </div>
                        <div class="panel-body">
                            <ul class="list-unstyled latest-users">
                                    <?php
                                        if(!empty($latestItems)){
                                            foreach($latestItems as $item){
                                                echo '<li>';
                                                    echo $item['Name'];
                                                    echo '<a href="items.php?do=Delete&itemid='.$item['Item_ID'].'">';
                                                        echo '<span class="btn btn-outline-danger pull-right">';
                                                            echo '<i class="fa fa-close"></i> Delete';
                                                        echo '</span>';
                                                    echo '</a>';
                                                    echo '<a href="items.php?do=Edit&itemsid='.$item['Item_ID'].'">';
                                                        echo '<span class="btn btn-outline-success pull-right">';
                                                            echo '<i class="fa fa-edit"></i> Edit'; 
                                                            if($item['Approve'] == 0){
                                                                echo"<a href='items.php?do=Approve&itemid="
                                                                .$item['Item_ID']
                                                                ."' class='btn btn-outline-info activate pull-right'>
                                                                <i class='fa fa-check'></i> Approve</a>" ; 
                                                            }
                                                        echo '</span>';
                                                    echo '</a>';
                                                echo '</li>';
                                            }
                                        }
                                        else{
                                            echo '<div class="alert alert-info">There Is No Items To Show </div>';
                                        }    
                                        ?>
                                </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--------------------------------------------------------------->
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-comments"></i> Latest <?php echo $numComments?> Comments
                        </div>
                        <div class="panel-body">
                        <ul class="list-unstyled latest-users">    
                            <?php
                                    $stmt =$con->prepare("SELECT 
                                                                comments.*, users.Username 
                                                            FROM 
                                                                comments
                                                            INNER JOIN
                                                                users
                                                            ON
                                                                users.UserID = comments.user_id
                                                            ORDER BY C_ID DESC    
                                                            LIMIT $numComments");
                                        $stmt->execute();
                                        // assign to variable
                                        $comments = $stmt->fetchAll();
                                        if(! empty($comments)){
                                            foreach ($comments as $comment) {
                                                echo '<li>';
                                                    echo '<div class="comment-box">';
                                                        echo '<span class="member-name">'. $comment['Username'] . '</span>';
                                                        echo '<p class="member-comment">'. $comment['Comments'] . '</p>';

                                                        echo '<a href="comments.php?do=Delete&comid='.$comment['C_ID'].'">';
                                                            echo '<span class="btn btn-outline-danger pull-right">';
                                                                echo '<i class="fa fa-close"></i> Delete';
                                                            echo '</span>';
                                                        echo '</a>';
                                                        echo '<a href="comments.php?do=Edit&comid='.$comment['C_ID'].'">';
                                                            echo '<span class="btn btn-outline-success pull-right">';
                                                                echo '<i class="fa fa-edit"></i> Edit'; 
                                                                if($comment['Status'] == 0){
                                                                    echo"<a href='comments.php?do=Approve&comid="
                                                                    .$comment['C_ID']
                                                                    ."' class='btn btn-outline-info activate pull-right'>
                                                                    <i class='fa fa-check'></i> Approve</a>" ; 
                                                                }
                                                            echo '</span>';
                                                        echo '</a>';

                                                    echo '</div>';
                                                echo '</li>';
                                            }
                                        }
                                        else{
                                            echo '<div class="alert alert-info">There Is No Comments To Show </div>';
                                        }    
                            ?>
                        </ul>        
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        /** end dashboard here  */
        include $tpl.'footer.php';
    }
    else{
        header('Location: index.php');
        exit();
    }

ob_end_flush();
?>