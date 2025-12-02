<?php
require('connect-db.php');
require('recipe-db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review_id = intval($_POST['review_id']);
    $recipe_id = intval($_POST['recipe_id']);

    $review = getReviewById($review_id);

    if ($review && $review['user_id'] == $_SESSION['user_id']) {
        deleteReview($review_id);
    }

    header("Location: recipe.php?id=$recipe_id");
    exit();
}
