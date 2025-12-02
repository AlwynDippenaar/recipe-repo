<?php
require('connect-db.php');
// require('request-db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Repo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="recipe.css">
</head>
<body>
    <?php include('header.php'); ?>

    <div class="container">
        <h1>Home</h1>
        <?php echo "Test Working !"; ?>
        <p><a href="https://www.figma.com/design/pHKg8WBmK02dQs0Ic1Npgl/Recipe-Repo?node-id=0-1&t=VmZuYC9p8hERfEtH-1">The Figma, for reference (delete this link when we're done)</a></p>


        <p>Find classic and modern recipes to suit any needs, restrictions, and available ingredients you have!</p>
        <!-- Would be great to have this button redirect to log in page rather than create recipe if not logged in -->
        <?php if (isset($_SESSION["username"])) { ?>
            <a class="btn btn-dark" href="create.php">Add Your Own Recipes</a>
        <?php } else { ?>
            <a class="btn btn-dark" href="login.php">Add Your Own Recipes</a>
        <?php }?>
        

        <h2 class="mt-5">Top Recipes</h2>
        <div class="container">
            <!-- put in top 3 recipes, idk how we define top -->
            <div class="row">
                <div class="col">
                    <img src="" alt="recipe 1">
                    <p>Recipe description</p>
                </div>
                <div class="col">
                    <img src="" alt="recipe 2">
                    <p>Recipe description</p>
                </div>
                <div class="col">
                    <img src="" alt="recipe 3">
                    <p>Recipe description</p>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION["username"])) { ?>
        <h2 class="mt-5">Recommended Recipe</h2>
        <div class="container">
            <!-- determine a recommended recipe from user preferences, just exclude if not logged in, ig -->
            <div class="row">
                <div class="col">
                    <strong>Suits Your Ingredients</strong>
                    <p>Recommended to you based on ___ ingredients you have on hand</p>
                    <strong>Suits Your Tastes</strong>
                    <p>Recommended to you based on ___ preferences you have expressed</p>
                    <strong>Suits Your Appliances</strong>
                    <p>Recommended to you based on ___ appliances and utensils you have available</p>
                </div>
                <div class="col">
                    <img src="" alt="big recipe image">
                </div>
            </div>
        </div>
        <?php } ?>
        
        <!-- could add a section for latest reviews, like on the Figma mockup, but idk if that rly makes sense -->
    </div>

    <?php include('footer.html'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>