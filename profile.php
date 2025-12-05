<?php
require('connect-db.php');
require('user-functions.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
$current_email = $_SESSION['email'];
$user_recipes = getRecipesByUser($user_id);
$favorited_recipes = getFavoritedRecipesByUser($user_id);

echo $user_id;

$success = "";
$error = "";

// ---------------------
// Update Username
// ---------------------
if (isset($_POST['update_username'])) {
    $new_username = trim($_POST['new_username']);

    if (updateUsername($user_id, $new_username)) {
        $_SESSION['username'] = $new_username; // Update session
        $success = "Username updated successfully.";
    } else {
        $error = "Name update failed. Try another.";
    }
}

// ---------------------
// Update Email
// ---------------------
if (isset($_POST['update_email'])) {
    $new_email = trim($_POST['new_email']);

    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email.";
    } else {
        if (updateEmail($user_id, $new_email)) {
            $_SESSION['email'] = $new_email; // Update session
            $success = "Email updated successfully.";
        } else {
            $error = "Email already taken. Choose another.";
        }
    }
}

// ---------------------
// Update Password
// ---------------------
if (isset($_POST['update_password'])) {
    $pass1 = $_POST['password'];
    $pass2 = $_POST['confirm_password'];

    if ($pass1 !== $pass2) {
        $error = "Passwords do not match.";
    } else {
        updatePassword($user_id, $pass1);
        $success = "Password updated successfully.";
    }
}

// ---------------------
// Delete Account
// ---------------------
if (isset($_POST['delete_account'])) {
    deleteUser($user_id);
    session_unset();
    session_destroy();
    header("Location: signup.php?deleted=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Recipe Repo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="recipe.css">
</head>
<body>

<?php include('header.php'); ?>

<div class="container mt-4">

    <h1>Your Profile</h1>
    <p><strong>Logged in as:</strong> <?= $current_username ?> | <?= htmlspecialchars($current_email) ?></p>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <h3>Favorites</h3>

    <?php if (empty($favorited_recipes)): ?>
        <p>You haven't favorited any recipes yet.</p>
    <?php else: ?>
        <div class="list-group mb-4">
            <?php foreach ($favorited_recipes as $recipe): ?>
                <a href="recipe.php?id=<?= $recipe['recipe_id'] ?>" class="list-group-item list-group-item-action">
                    <h5 class="mb-1"><?= htmlspecialchars($recipe['recipe_title']) ?></h5>

                    <div><small>Cook Time: <?= htmlspecialchars($recipe['cook_time']) ?> mins</small></div>
                    <div><small>Created on <?= htmlspecialchars($recipe['recipe_date_time']) ?></small></div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h3>Your Recipes</h3>

    <?php if (empty($user_recipes)): ?>
        <p>You haven't written any recipes yet.</p>
    <?php else: ?>
        <div class="list-group mb-4">
            <?php foreach ($user_recipes as $recipe): ?>
                <a href="recipe.php?id=<?= $recipe['id'] ?>" class="list-group-item list-group-item-action">
                    <h5 class="mb-1"><?= htmlspecialchars($recipe['recipe_title']) ?></h5>

                    <div><small>Cook Time: <?= htmlspecialchars($recipe['cook_time']) ?></small></div>
                    <div><small>Created on <?= htmlspecialchars($recipe['recipe_date_time'] ?? 'Unknown date') ?></small></div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


    <h3>[Need some sections for adding preferences, appliances, etc.]</h3>


    <h3>Manage Your Account</h3>

    <!-- Update Username -->
    <h4 class="mt-4">Update Username</h4>
    <form method="POST" class="mb-4" style="max-width: 400px;">
        <input type="text" name="new_username" class="form-control mb-2" placeholder="New username" value=<?= $current_username?> required>
        <button name="update_username" class="btn btn-dark">Update Username</button>
    </form>

    <!-- Update Email -->
    <h4 class="mt-4">Update Email</h4>
    <form method="POST" class="mb-4" style="max-width: 400px;">
        <input type="email" name="new_email" class="form-control mb-2" placeholder="New email" value=<?= $current_email?> required>
        <button name="update_email" class="btn btn-dark">Update Email</button>
    </form>

    <!-- Update Password -->
    <h4>Update Password</h4>
    <form method="POST" class="mb-4" style="max-width: 400px;">
        <input type="password" name="password" class="form-control mb-2" placeholder="New password" required>
        <input type="password" name="confirm_password" class="form-control mb-2" placeholder="Confirm password" required>
        <button name="update_password" class="btn btn-dark">Update Password</button>
    </form>

    <!-- Delete Account -->
    <h4>Delete Account</h4>
    <form method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.');">
        <button name="delete_account" class="btn btn-danger mb-5">Delete My Account</button>
    </form>

</div>

<?php include('footer.html'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
