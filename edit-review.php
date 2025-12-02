<?php
require('connect-db.php');
require('recipe-db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("No review specified.");
}

$review_id = intval($_GET['id']);
$review = getReviewById($review_id);

if (!$review || $review['user_id'] != $_SESSION['user_id']) {
    die("You cannot edit this review.");
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
        updateReview($review_id, $title, $msg, $rating);
        header("Location: recipe.php?id=" . $review['recipe_id']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include('header.php'); ?>

<div class="container mt-4">
    <h1>Edit Review</h1>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="review_title" class="form-control" required
                   value="<?php echo htmlspecialchars($review['review_title']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="5" required><?php echo htmlspecialchars($review['message']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select" required>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php if ($review['rating'] == $i) echo 'selected'; ?>>
                        <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <button class="btn btn-dark" type="submit">Save Changes</button>
        <a href="recipe.php?id=<?php echo $review['recipe_id']; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('footer.html'); ?>
</body>
</html>
