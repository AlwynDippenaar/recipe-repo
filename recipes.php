<?php
require('connect-db.php');
require('recipe-db.php');
require('user-functions.php');
session_start();

$user_id = $_SESSION['user_id'] ?? null;

$all_prefs = getAllUserPreferences();
$user_prefs = $user_id ? getUserPreferences($user_id) : [];
$user_pref_ids = array_column($user_prefs, 'pref_id');

$selected_pref_ids = $_GET['prefs'] ?? [];
$selected_pref_ids = array_map('intval', $selected_pref_ids); 
$results = [];

if (isset($_GET['q']) && trim($_GET['q']) !== "") {
    $query = trim($_GET['q']);
    $results = searchRecipes($query);
} else if (!empty($selected_pref_ids)) {
    $results = searchRecipesByPreferences($selected_pref_ids);
} else {
    $results = getRandomRecipes(10);
}
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

        <?php if ($user_id): ?>
            <div class="mt-3">
                <label><strong>Filter by Preferences:</strong></label><br>
                <?php foreach ($user_prefs as $pref): ?>
                    <?php
                    $checked = in_array($pref['pref_id'], $selected_pref_ids) ? 'checked' : '';
                    ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="prefs[]" value="<?= $pref['pref_id'] ?>" id="pref-<?= $pref['pref_id'] ?>" <?= $checked ?>>
                        <label class="form-check-label" for="pref-<?= $pref['pref_id'] ?>">
                            <?= htmlspecialchars(ucfirst($pref['include_or_exclude'])) ?>: <?= htmlspecialchars($pref['title']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <input type="submit" value="Search" class="btn btn-dark mt-2">
    </form>

    <br><br>

    <?php if (!empty($results)): ?>
        <h2>
            <?php
            if (isset($_GET['q']) && trim($_GET['q']) !== "") {
                echo "Matching Recipes";
            } else {
                echo "Recommended Recipes";
            }
            ?>
        </h2>

        <div class="row">
            <?php foreach ($results as $recipe): ?>
                <div class="col-md-4 mb-4">
                    <div class="card p-3 shadow-sm">
                        <h4><?php echo htmlspecialchars($recipe['recipe_title']); ?></h4>
                        <p class="text-muted">
                            <?php echo $recipe['description'] ? htmlspecialchars(substr($recipe['description'], 0, 100)) . "..." : "No description provided"; ?>
                        </p>
                        <p><strong>Cook Time:</strong> <?php echo htmlspecialchars($recipe['cook_time']); ?> mins</p>
                        <p><strong>Author:</strong> <?php echo getAuthorById($recipe['user_id'])["username"]; ?></p>
                        <p><strong>Posted:</strong> <?php echo htmlspecialchars($recipe['recipe_date_time']); ?></p>
                        <a href="recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-primary btn-sm">
                            View Recipe
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php elseif (isset($_GET['q']) || !empty($selected_pref_ids)): ?>
        <p>No recipes found.</p>
    <?php endif; ?>

</div>

<?php include('footer.html'); ?>
</body>
</html>
