<?php

function getFavoritedRecipesByUser($user_id)
{
    global $db;

    $query = "
        SELECT r.*
        FROM user_favourites uf
        INNER JOIN recipe r ON uf.recipe_id = r.recipe_id
        WHERE uf.user_id = :uid
        ORDER BY r.recipe_id DESC
    ";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return $results;
}




function getRecipesByUser($user_id)
{
    global $db;

    $query = "SELECT * FROM recipe WHERE user_id = :uid ORDER BY recipe_id DESC";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return $results;
}


function getRecipes($count)
{
    global $db;

    $count = (int)$count;
    if ($count <= 0) {
        return [];
    }

    $query = "SELECT * FROM recipe ORDER BY recipe_id DESC LIMIT :count";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':count', $count, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return $results;
}


function createUser($username, $email, $password)
{
    global $db;

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password)
              VALUES (:username, :email, :password)";

    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $hashed);
        $statement->execute();
        $statement->closeCursor();
        return true;
    } catch (PDOException $e) {
        return false; // Usually duplicate email
    }
}

function getUserByEmail($email)
{
    global $db;

    $query = "SELECT * FROM users WHERE email = :email";

    $statement = $db->prepare($query);
    $statement->bindValue(':email', $email);
    $statement->execute();
    $result = $statement->fetch();
    $statement->closeCursor();

    return $result;
}

function attemptLogin($email, $password)
{
    $user = getUserByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}

function updateEmail($user_id, $new_email)
{
    global $db;

    $query = "UPDATE users SET email = :email WHERE user_id = :uid";

    try {
        $stmt = $db->prepare($query);
        $stmt->bindValue(':email', $new_email);
        $stmt->bindValue(':uid', $user_id);
        $stmt->execute();
        $stmt->closeCursor();
        return true;

    } catch (PDOException $e) {
        // email probably already exists
        return false;
    }
}

function updateUsername($user_id, $new_username)
{
    global $db;

    $query = "UPDATE users SET username = :uname WHERE user_id = :uid";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':uname', $new_username);
    $stmt->bindValue(':uid', $user_id);
    $stmt->execute();
    $stmt->closeCursor();
}

function updatePassword($user_id, $new_password)
{
    global $db;

    $hashed = password_hash($new_password, PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = :pwd WHERE user_id = :uid";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':pwd', $hashed);
    $stmt->bindValue(':uid', $user_id);
    $stmt->execute();
    $stmt->closeCursor();
}

function deleteUser($user_id)
{
    global $db;

    $query = "DELETE FROM users WHERE user_id = :uid";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':uid', $user_id);
    $stmt->execute();
    $stmt->closeCursor();
}


?>
