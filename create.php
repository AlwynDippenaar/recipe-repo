<?php
require('connect-db.php');
require('user-functions.php');
require('recipe-db.php');    // includes insertRecipe(), insertRecipeContains(), insertRecipeUses(), insertInstruction()
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$all_prefs = getAllUserPreferences();
$selected_prefs = $_POST['recipe_preferences'] ?? [];

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['recipe_title']);
    $desc = trim($_POST['description']);
    $cook_time = intval($_POST['cook_time']);
    $ingredients = $_POST['ingredient'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $units = $_POST['unit'] ?? [];
    $kitchenware = $_POST['kitchenware'] ?? [];
    $instructions = $_POST['instruction_text'] ?? [];

    if (empty($title) || empty($desc) || empty($cook_time)) {
        $message = "Please fill in the title, description, and cook time.";
    } elseif (empty($ingredients)) {
        $message = "Please add at least one ingredient.";
    } elseif (empty(array_filter($instructions))) {
        $message = "Please add at least one instruction.";
    } else {
        // Insert recipe
        $recipe_id = insertRecipe($title, $desc, $cook_time, $_SESSION['user_id']);

        // Insert ingredients
        foreach ($ingredients as $i => $ingredient_name) {
            $ingredient_name = trim($ingredient_name);
            $qty = trim($quantities[$i]);
            $unit = trim($units[$i]);
            if ($ingredient_name != "" && $qty != "") {
                insertRecipeContains($recipe_id, $ingredient_name, $qty, $unit);
            }
        }

        // Insert kitchenware
        foreach ($kitchenware as $kw_name) {
            $kw_name = trim($kw_name);
            if ($kw_name != "") {
                insertRecipeUses($recipe_id, $kw_name);
            }
        }

        // Insert instructions
        $step = 1;
        foreach ($instructions as $text) {
            $text = trim($text);
            if ($text !== "") {
                insertInstruction($recipe_id, $step, $text);
                $step++;
            }
        }

        // Insert recipe preferences
        foreach ($recipe_prefs as $pref_id) {
            $pref_id = (int)$pref_id;
            $query = "INSERT INTO recipe_satisfies (recipe_id, pref_id) VALUES (:rid, :pid)";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':rid', $recipe_id, PDO::PARAM_INT);
            $stmt->bindValue(':pid', $pref_id, PDO::PARAM_INT);
            $stmt->execute();
}


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
    <title>Create Recipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="recipe.css">
</head>
<body>
<?php include('header.php'); ?>

<div class="container mt-4">
    <h1>Create a New Recipe</h1>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="recipe_title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="5" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Cook Time (minutes)</label>
            <input type="number" name="cook_time" class="form-control" required>
        </div>

        <!-- INGREDIENTS -->
        <h4>Ingredients</h4>
        <div id="ingredients-list">
            <div class="row mb-2 ingredient-row">
                <div class="col">
                    <input type="text" name="ingredient[]" class="form-control" placeholder="Ingredient name" required>
                </div>
                <div class="col-3">
                    <input type="text" name="quantity[]" class="form-control" placeholder="Quantity" required>
                </div>
                <div class="col-3">
                    <input type="text" name="unit[]" class="form-control" placeholder="Unit">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger remove-ingredient">&times;</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mb-3" id="add-ingredient">Add Ingredient</button>

        <!-- KITCHENWARE -->
        <h4>Kitchenware</h4>
        <div id="kitchenware-list">
            <div class="row mb-2 kw-row">
                <div class="col">
                    <input type="text" name="kitchenware[]" class="form-control" placeholder="Kitchenware item">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger remove-kw">&times;</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mb-3" id="add-kw">Add Kitchenware</button>

        <!-- INSTRUCTIONS -->
        <h4>Instructions</h4>
        <div id="instructions-list">
            <div class="row mb-2 instruction-row">
                <div class="col-11">
                    <textarea name="instruction_text[]" class="form-control" placeholder="Step description" required></textarea>
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger remove-instruction">&times;</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mb-3" id="add-instruction">Add Instruction</button>

        <br>

        <!-- Recipe Preferences -->
        <h4>Recipe Preferences</h4>
        <p>Select which preferences this recipe satisfies:</p>
        <div class="mb-3">
            <?php foreach ($all_prefs as $pref): ?>
                <div class="form-check form-check-inline">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        name="recipe_preferences[]" 
                        value="<?= $pref['pref_id'] ?>" 
                        id="pref<?= $pref['pref_id'] ?>"
                        <?= in_array($pref['pref_id'], $selected_prefs) ? 'checked' : '' ?>
                    >
                    <label class="form-check-label" for="pref<?= $pref['pref_id'] ?>">
                        <?= htmlspecialchars($pref['title']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-success mt-4 mb-4">Post Recipe!</button>
    </form>
</div>

<script>
// --- INGREDIENTS ---
document.getElementById('add-ingredient').addEventListener('click', () => {
    const container = document.getElementById('ingredients-list');
    const div = document.createElement('div');
    div.classList.add('row', 'mb-2', 'ingredient-row');
    div.innerHTML = `
        <div class="col">
            <input type="text" name="ingredient[]" class="form-control" placeholder="Ingredient name" required>
        </div>
        <div class="col-3">
            <input type="text" name="quantity[]" class="form-control" placeholder="Quantity" required>
        </div>
        <div class="col-3">
            <input type="text" name="unit[]" class="form-control" placeholder="Unit">
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-danger remove-ingredient">&times;</button>
        </div>`;
    container.appendChild(div);
});

document.addEventListener('click', function(e){
    if(e.target && e.target.classList.contains('remove-ingredient')){
        e.target.closest('.ingredient-row').remove();
    }
});

// --- KITCHENWARE ---
document.getElementById('add-kw').addEventListener('click', () => {
    const container = document.getElementById('kitchenware-list');
    const div = document.createElement('div');
    div.classList.add('row', 'mb-2', 'kw-row');
    div.innerHTML = `
        <div class="col">
            <input type="text" name="kitchenware[]" class="form-control" placeholder="Kitchenware item">
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-danger remove-kw">&times;</button>
        </div>`;
    container.appendChild(div);
});

document.addEventListener('click', function(e){
    if(e.target && e.target.classList.contains('remove-kw')){
        e.target.closest('.kw-row').remove();
    }
});

// --- INSTRUCTIONS ---
document.getElementById('add-instruction').addEventListener('click', () => {
    const container = document.getElementById('instructions-list');
    const div = document.createElement('div');
    div.classList.add('row', 'mb-2', 'instruction-row');
    div.innerHTML = `
        <div class="col-11">
            <textarea name="instruction_text[]" class="form-control" placeholder="Step description" required></textarea>
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-danger remove-instruction">&times;</button>
        </div>`;
    container.appendChild(div);
});

document.addEventListener('click', function(e){
    if(e.target && e.target.classList.contains('remove-instruction')){
        e.target.closest('.instruction-row').remove();
    }
});
</script>

<?php include('footer.html'); ?>
</body>
</html>
