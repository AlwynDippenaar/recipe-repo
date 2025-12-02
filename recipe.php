<?php
require('connect-db.php');
require('recipe-db.php');    

session_start();

if (!isset($_GET['id'])) {
    die("No recipe specified.");
}

$recipe_id = intval($_GET['id']);
$recipe = getRecipeById($recipe_id);

if (!$recipe) {
    die("Recipe not found.");
}

$author = getAuthorById($recipe['user_id']);
$ingredients = getIngredientsByRecipe($recipe_id);
$kitchenware = getKitchenwareByRecipe($recipe_id);
$reviews = getReviewsByRecipe($recipe_id);

$is_fav = false;
if (isset($_SESSION['user_id'])) {
    $is_fav = userFavorited($_SESSION['user_id'], $recipe_id);
}

// Handle favourite toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    if (isset($_POST['favorite'])) {
        addFavorite($_SESSION['user_id'], $recipe_id);
        $is_fav = true;
    }
    if (isset($_POST['unfavorite'])) {
        removeFavorite($_SESSION['user_id'], $recipe_id);
        $is_fav = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe['recipe_title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="recipe.css">
</head>

<body>
<?php include('header.php'); ?>

<div class="container mt-4">

    <h1><?php echo htmlspecialchars($recipe['recipe_title']); ?></h1>

    <p class="text-muted">
        By <strong><?php echo htmlspecialchars($author['username']); ?></strong> ·
        Posted on <?php echo htmlspecialchars($recipe['recipe_date_time']); ?>
    </p>

    <div class="mb-3">
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST">
                <?php if ($is_fav): ?>
                    <button class="btn btn-warning" name="unfavorite">★ Remove from Favorites</button>
                <?php else: ?>
                    <button class="btn btn-outline-warning" name="favorite">☆ Add to Favorites</button>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>

    <hr>

    <h4>Description</h4>
    <p><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>

    <strong>Cook Time:</strong>
    <span><?php echo htmlspecialchars($recipe['cook_time']); ?> minutes</span>

    <hr>

    <h3>Ingredients</h3>
    <?php if (empty($ingredients)): ?>
        <p>No ingredients listed.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($ingredients as $ing): ?>
            <li>
                <?php echo htmlspecialchars($ing['ingredient_name']); ?>:
                <?php echo htmlspecialchars($ing['quantity']); ?>
                <?php echo htmlspecialchars($ing['unit']); ?>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h3>Required Kitchenware</h3>
    <?php if (empty($kitchenware)): ?>
        <p>No kitchenware listed.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($kitchenware as $kw): ?>
                <li><?php echo htmlspecialchars($kw['kw_name']); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr>

    <h3>Reviews</h3>
    <a class="btn btn-dark mb-3" href="add-review.php?recipe_id=<?php echo $recipe_id; ?>">Write a Review</a>

    <?php if (empty($reviews)): ?>
        <p>No reviews yet.</p>
    <?php else: ?>
        <?php foreach ($reviews as $rev): ?>
    <div class="card p-3 mb-3">
        <h5><?php echo htmlspecialchars($rev['review_title']); ?></h5>
        <p class="text-muted">
            Rating: <?php echo htmlspecialchars($rev['rating']); ?>/5 ·
            <?php echo htmlspecialchars($rev['review_date_time']); ?>
        </p>
        <p><?php echo nl2br(htmlspecialchars($rev['message'])); ?></p>
        <p><em>- <?php echo htmlspecialchars($rev['username']); ?></em></p>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $rev['user_id']): ?>
            <div class="container">
                <a href="edit-review.php?id=<?php echo $rev['review_id']; ?>" class="btn btn-sm btn-primary">Edit</a>

                <form method="POST" action="delete-review.php" class="d-inline">
                    <input type="hidden" name="review_id" value="<?php echo $rev['review_id']; ?>">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                    <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this review?')">
                        Delete
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

    <?php endif; ?>

    <a href="recipes.php" class="btn btn-secondary mt-3 mb-4">Back to Search</a>

</div>

<?php include('footer.html'); ?>
</body>
</html>
