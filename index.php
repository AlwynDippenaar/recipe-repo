<?php
require('connect-db.php');
require('user-functions.php');
session_start();

$random_recipes = getRandomRecipes(10);
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

        <p>Find classic and modern recipes to suit any needs, restrictions, and available ingredients you have!</p>

        <?php if (isset($_SESSION["username"])) { ?>
            <a class="btn btn-dark" href="create.php">Add Your Own Recipes</a>
        <?php } else { ?>
            <a class="btn btn-dark" href="login.php">Add Your Own Recipes</a>
        <?php } ?>
        
        <?php if (isset($_SESSION["username"])) { ?>
            <h2 class="mt-5">Recommended Recipes</h2>

            <div class="list-group mb-4">
                <?php foreach ($random_recipes as $recipe): ?>
                    <a href="recipe.php?id=<?= $recipe['recipe_id'] ?>" class="list-group-item list-group-item-action">
                        <h5 class="mb-1"><?= htmlspecialchars($recipe['recipe_title']) ?></h5>
                        <div><small>Cook Time: <?= htmlspecialchars($recipe['cook_time']) ?> mins</small></div>
                        <div><small>Created on <?= htmlspecialchars($recipe['recipe_date_time']) ?></small></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php } ?>
        
    </div>

    <?php include('footer.html'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
