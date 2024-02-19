
<nav class="navbar navbar-expand-lg navbar-light bg-light ">
  <a class="navbar-brand" href="#">Brand</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#app-nav" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse justify-content-center" id="app-nav">
    <ul class="navbar-nav ">
      <li class="nav-item active">
        <a class="nav-link" href="dashboard.php"><?php echo lang('HOME_ADMIN')?> <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <li> <a class="nav-link" href="categories.php">   <?php echo lang('Categories')?> </a>  </li>
        <li> <a class="nav-link" href="items.php">        <?php echo lang('Items')?>      </a>  </li>
        <li> <a class="nav-link" href="members.php">      <?php echo lang('Members')?>    </a>  </li>
        <li> <a class="nav-link" href="comments.php">     <?php echo lang('Comments')?>   </a>  </li>
      </li>
      <li class="nav-item dropdown" style="background-color: #1498db;border-radius:6px;">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#fff;">
          Paula
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color: #1498db;">
          <a class="dropdown-item" href="members.php?do=Edit&userid=<?php echo $_SESSION['ID'] ?>">Edit Profile</a>
          <a class="dropdown-item" href="../index.php">Visit Shop</a>
          <a class="dropdown-item" href="#">Settings</a>
          <a class="dropdown-item" href="logout.php">Logout</a>
        </div>
      </li>
    </ul>
  </div>
</nav>