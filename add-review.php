<?php
require('connect-db.php');
require('recipe-db.php');   // we'll add insertReview() here
require('user-functions.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['recipe_id'])) {
    die("No recipe specified.");
}

$recipe_id = intval($_GET['recipe_id']);
$recipe = getRecipeById($recipe_id);

if (!$recipe) {
    die("Recipe not found.");
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['review_title']);
    $msg = trim($_POST['message']);
    $rating = intval($_POST['rating']);

    if ($rating < 1 || $rating > 5) {
        $message = "Rating must be between 1 and 5.";
    } elseif (empty($title) || empty($msg)) {
        $message = "Please fill in all fields.";
    } else {
        insertReview($recipe_id, $_SESSION['user_id'], $title, $msg, $rating);
        header("Location: recipe.php?id=$recipe_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a Review | <?php echo htmlspecialchars($recipe['recipe_title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="recipe.css">
</head>
<body>

<?php include('header.php'); ?>

<div class="container mt-4">
    <h1>Write a Review for "<?php echo htmlspecialchars($recipe['recipe_title']); ?>"</h1>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-3 mb-4">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="review_title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="5" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select" required>
                <option value="">Select rating</option>
                <option value="1">1 - Poor</option>
                <option value="2">2 - Fair</option>
                <option value="3">3 - Good</option>
                <option value="4">4 - Very Good</option>
                <option value="5">5 - Excellent</option>
            </select>
        </div>

        <button class="btn btn-dark" type="submit">Submit Review</button>
        <a href="recipe.php?id=<?php echo $recipe_id; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('footer.html'); ?>
</body>
</html>
