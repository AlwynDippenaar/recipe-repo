<?php session_start(); ?>

<header>  
  <nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">            
      <a class="navbar-brand" href="#">Recipe Repo</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar" aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav ms-auto">
        
          <li class="nav-item">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="recipes.php">Recipes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="create.php">Create</a>
          </li>

          <?php if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) { ?>              
            <li class="nav-item">
              <a class="nav-link" href="signup.php">Sign up</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="login.php">Log in</a>
            </li>              
          <?php  } else { ?>                    
            <li class="nav-item">                  
              <a class="nav-link" href="profile.php"><?php echo $_SESSION['username'];?></a>
            </li>
            <li class="nav-item">                  
              <a class="nav-link" href="logout.php">Log Out</a>
            </li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </nav>
</header>    