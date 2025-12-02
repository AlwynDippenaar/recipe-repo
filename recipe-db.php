<?php
function searchRecipes($query)
{
    global $db;

    $sql = "SELECT *
            FROM recipe
            WHERE recipe_title LIKE :q
               OR description LIKE :q
            ORDER BY recipe_date_time DESC";

    $statement = $db->prepare($sql);
    $statement->bindValue(':q', '%' . $query . '%');
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();

    return $results;
}

function getAuthorById($user_id){
    global $db;

    $query = "SELECT * FROM users WHERE user_id = :uid";

    $statement = $db->prepare($query);
    $statement->bindValue(':uid', $user_id);
    $statement->execute();
    $result = $statement->fetch();
    $statement->closeCursor();

    return $result;
}

function getRecipeById($id)
{
    global $db;

    $query = "SELECT * FROM recipe WHERE recipe_id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);
    $statement->execute();
    $recipe = $statement->fetch();
    $statement->closeCursor();

    return $recipe;
}

function getIngredientsByRecipe($recipe_id)
{
    global $db;

    $query = "SELECT i.ingredient_name, rc.quantity, rc.unit
              FROM recipe_contains rc
              JOIN ingredient i ON rc.ingredient_id = i.ingredient_id
              WHERE rc.recipe_id = :id";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':id', $recipe_id);
    $stmt->execute();
    $results = $stmt->fetchAll();
    $stmt->closeCursor();
    return $results;
}

function getKitchenwareByRecipe($recipe_id)
{
    global $db;

    $query = "SELECT k.kw_name
              FROM recipe_uses ru
              JOIN kitchenware k ON ru.kw_id = k.kw_id
              WHERE ru.recipe_id = :id";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':id', $recipe_id);
    $stmt->execute();
    $results = $stmt->fetchAll();
    $stmt->closeCursor();
    return $results;
}

function getReviewsByRecipe($recipe_id)
{
    global $db;

    $query = "SELECT r.*, u.username
              FROM review r
              JOIN users u ON r.user_id = u.user_id
              WHERE r.recipe_id = :id
              ORDER BY r.review_date_time DESC";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':id', $recipe_id);
    $stmt->execute();
    $results = $stmt->fetchAll();
    $stmt->closeCursor();
    return $results;
}

function userFavorited($user_id, $recipe_id)
{
    global $db;

    $query = "SELECT * FROM user_favourites 
              WHERE user_id = :uid AND recipe_id = :rid";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':uid', $user_id);
    $stmt->bindValue(':rid', $recipe_id);
    $stmt->execute();
    $result = $stmt->fetch();
    $stmt->closeCursor();
    return $result ? true : false;
}

function addFavorite($user_id, $recipe_id)
{
    global $db;

    $query = "INSERT INTO user_favourites (user_id, recipe_id)
              VALUES (:uid, :rid)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':uid', $user_id);
    $stmt->bindValue(':rid', $recipe_id);
    $stmt->execute();
    $stmt->closeCursor();
}

function removeFavorite($user_id, $recipe_id)
{
    global $db;

    $query = "DELETE FROM user_favourites 
              WHERE user_id = :uid AND recipe_id = :rid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':uid', $user_id);
    $stmt->bindValue(':rid', $recipe_id);
    $stmt->execute();
    $stmt->closeCursor();
}

function insertReview($recipe_id, $user_id, $title, $message, $rating)
{
    global $db;

    $query = "INSERT INTO review (review_title, message, rating, review_date_time, user_id, recipe_id)
              VALUES (:title, :msg, :rating, NOW(), :uid, :rid)";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':msg', $message);
    $stmt->bindValue(':rating', $rating);
    $stmt->bindValue(':uid', $user_id);
    $stmt->bindValue(':rid', $recipe_id);
    $stmt->execute();
    $stmt->closeCursor();
}

// Fetch a single review
function getReviewById($review_id) {
    global $db;
    $query = "SELECT * FROM review WHERE review_id = :rid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':rid', $review_id);
    $stmt->execute();
    $result = $stmt->fetch();
    $stmt->closeCursor();
    return $result;
}

// Update a review
function updateReview($review_id, $title, $message, $rating) {
    global $db;
    $query = "UPDATE review
              SET review_title = :title, message = :msg, rating = :rating, review_date_time = NOW()
              WHERE review_id = :rid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':msg', $message);
    $stmt->bindValue(':rating', $rating);
    $stmt->bindValue(':rid', $review_id);
    $stmt->execute();
    $stmt->closeCursor();
}

// Delete a review
function deleteReview($review_id) {
    global $db;
    $query = "DELETE FROM review WHERE review_id = :rid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':rid', $review_id);
    $stmt->execute();
    $stmt->closeCursor();
}

// Insert recipe
function insertRecipe($title, $desc, $cook_time, $user_id){
    global $db;
    $query = "INSERT INTO recipe (recipe_title, description, cook_time, recipe_date_time, user_id)
              VALUES (:title, :desc, :cook_time, NOW(), :uid)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':desc', $desc);
    $stmt->bindValue(':cook_time', $cook_time);
    $stmt->bindValue(':uid', $user_id);
    $stmt->execute();
    $recipe_id = $db->lastInsertId();
    $stmt->closeCursor();
    return $recipe_id;
}

// Insert ingredient (auto-insert ingredient if not exists)
function insertRecipeContains($recipe_id, $ingredient_name, $quantity, $unit){
    global $db;

    // check if ingredient exists
    $stmt = $db->prepare("SELECT ingredient_id FROM ingredient WHERE ingredient_name = :name");
    $stmt->bindValue(':name', $ingredient_name);
    $stmt->execute();
    $ing = $stmt->fetch();
    if ($ing) {
        $ingredient_id = $ing['ingredient_id'];
    } else {
        $stmt2 = $db->prepare("INSERT INTO ingredient (ingredient_name) VALUES (:name)");
        $stmt2->bindValue(':name', $ingredient_name);
        $stmt2->execute();
        $ingredient_id = $db->lastInsertId();
        $stmt2->closeCursor();
    }
    $stmt->closeCursor();

    $stmt3 = $db->prepare("INSERT INTO recipe_contains (recipe_id, ingredient_id, quantity, unit)
                           VALUES (:rid, :iid, :qty, :unit)");
    $stmt3->bindValue(':rid', $recipe_id);
    $stmt3->bindValue(':iid', $ingredient_id);
    $stmt3->bindValue(':qty', $quantity);
    $stmt3->bindValue(':unit', $unit);
    $stmt3->execute();
    $stmt3->closeCursor();
}

// Insert kitchenware (auto-insert kw if not exists)
function insertRecipeUses($recipe_id, $kw_name){
    global $db;
    // check if kitchenware exists
    $stmt = $db->prepare("SELECT kw_id FROM kitchenware WHERE kw_name = :name");
    $stmt->bindValue(':name', $kw_name);
    $stmt->execute();
    $kw = $stmt->fetch();
    if ($kw) {
        $kw_id = $kw['kw_id'];
    } else {
        $stmt2 = $db->prepare("INSERT INTO kitchenware (kw_name) VALUES (:name)");
        $stmt2->bindValue(':name', $kw_name);
        $stmt2->execute();
        $kw_id = $db->lastInsertId();
        $stmt2->closeCursor();
    }
    $stmt->closeCursor();

    $stmt3 = $db->prepare("INSERT INTO recipe_uses (recipe_id, kw_id) VALUES (:rid, :kwid)");
    $stmt3->bindValue(':rid', $recipe_id);
    $stmt3->bindValue(':kwid', $kw_id);
    $stmt3->execute();
    $stmt3->closeCursor();
}

?>
