<?php
require('connect-db.php');
require('recipe-db.php');   // <- file with your recipe search function
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Repo | Recipes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="recipe.css">
</head>

<body>
<?php include('header.php'); ?>

<div class="container">
    <h1>Find Recipes</h1>

    <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="search-bar">Search</label><br>
        <input name="q" id="search-bar" type="text" class="form-control"
               value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">

        <input type="submit" value="Search" class="btn btn-dark mt-2">
    </form>

    <br><br>

    <?php
    $results = [];

    if (isset($_GET['q']) && trim($_GET['q']) !== "") {
        $query = trim($_GET['q']);
        $results = searchRecipes($query);
    }

    if (!empty($results)):
    ?>
        <h2>Matching Recipes</h2>
        <div class="row">
            <?php foreach ($results as $recipe): ?>
                <div class="col-md-4 mb-4">
                    <div class="card p-3 shadow-sm">
                        <h4><?php echo htmlspecialchars($recipe['recipe_title']); ?></h4>

                        <p class="text-muted">
                            <?php echo $recipe['description'] ? htmlspecialchars(substr($recipe['description'], 0, 100))."..." : "No description provided"; ?>
                        </p>

                        <p><strong>Cook Time:</strong>
                           <?php echo htmlspecialchars($recipe['cook_time']); ?> mins</p>

                        <p><strong>Author:</strong>
                           <?php echo getAuthorById($recipe['user_id'])["username"]; ?></p>

                        <p><strong>Posted:</strong>
                           <?php echo htmlspecialchars($recipe['recipe_date_time']); ?></p>

                        <a href="recipe.php?id=<?php echo $recipe['recipe_id']; ?>" 
                           class="btn btn-primary btn-sm">
                           View Recipe
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php elseif (isset($_GET['q'])): ?>
        <p>No recipes found.</p>
    <?php endif; ?>

</div>

<?php include('footer.html'); ?>
</body>
</html>
