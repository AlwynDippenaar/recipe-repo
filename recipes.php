<?php
require('connect-db.php');
// require('request-db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Repo | Recipes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="recipe.css">
</head>
<body>
    <?php include('header.php'); ?>

    <div class="container">
        <h1>Find Recipes</h1>

        <form action="GET" action="<?php $_SERVER['PHP_SELF'] ?>" onsubmit="return validateInput()">
            <label for="tags-btn">Tags</label>
            <br>
            <button name="tags-btn" class="btn btn-dark">Add Tags</button>
            <!-- list out selected tags here -->

            <br>
            <br>

            <label for="search-bar">Search</label>
            <br>
            <input name="search-bar" type="text" class="form-control">
            <input type="submit" value="Search" class="btn btn-dark">

            <br>
            <br>
            <h2>Matching Recipes</h2>

            <!-- display grid of search results -->

        </form>
    </div>

    <?php include('footer.html'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>