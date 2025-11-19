<?php
try {
    $mysqlClient = new PDO('mysql:host=localhost;dbname=jo;charset=utf8', 'root', '');
    $mysqlClient->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die($e->getMessage());
}
 
if (!empty($_POST)) {
    $nom = trim($_POST['nom']);
    $pays = trim($_POST['pays']);
    $course = trim($_POST['course']);
    $temps = trim($_POST['temps']);
 
    if (strlen($pays) === 3 && is_numeric($temps)) {
        $pays = strtoupper($pays);
        $sqlInsert = 'INSERT INTO jo.`100` (nom, pays, course, temps) VALUES (:nom, :pays, :course, :temps)';
        $stmt = $mysqlClient->prepare($sqlInsert);
        $stmt->execute([
            'nom' => $nom,
            'pays' => $pays,
            'course' => $course,
            'temps' => $temps,
        ]);
    }
}
 
$allowed_columns = ["nom", "pays", "course", "temps"];
$allowed_orders = ["asc", "desc"];
 
$sort = "temps";
$order = "asc";
 
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_columns)) {
    $sort = $_GET['sort'];
}
if (isset($_GET['order']) && in_array($_GET['order'], $allowed_orders)) {
    $order = $_GET['order'];
}
 
$sql = "SELECT * FROM jo.`100` ORDER BY " . $sort . " " . $order;
$query = $mysqlClient->prepare($sql);
$query->execute();
$data = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats JO</title>
    <style>
        body { font-family: serif; padding: 20px; }
        h1 { font-family: serif; font-weight: bold; font-size: 2em; margin-bottom: 20px; }
        .form-container { margin-bottom: 40px; }
        .form-row { margin-bottom: 5px; }
        label { display: inline-block; width: 80px; font-weight: bold; }
        input { border: 1px solid #999; padding: 2px; width: 200px; }
        button { margin-top: 5px; cursor: pointer; background: #e9e9e9; border: 1px solid #999; padding: 2px 8px; border-radius: 3px; }
        table { border-collapse: collapse; width: 100%; max-width: 800px; font-family: serif; }
        th { text-align: left; padding: 10px 5px; border-bottom: none; font-weight: bold; }
        td { padding: 5px; }
        a { text-decoration: none; color: blue; }
        .arrows { font-size: 0.9em; margin-left: 5px; }
        .arrows a { text-decoration: none; margin: 0 1px; border-bottom: 1px solid blue; }
        .active-arrow { color: red !important; border-bottom: 1px solid red !important; font-weight: bold; }
    </style>
</head>
<body>
<div class="form-container">
    <h1>Ajouter un résultat :</h1>
    <form method="POST">
        <div class="form-row">
            <label>Nom :</label>
            <input type="text" name="nom" required>
        </div>
        <div class="form-row">
            <label>Pays :</label>
            <input type="text" name="pays" required>
        </div>
        <div class="form-row">
            <label>Course :</label>
            <input type="text" name="course" required>
        </div>
        <div class="form-row">
            <label>Temps :</label>
            <input type="text" name="temps" required>
        </div>
        <button type="submit">Valider</button>
    </form>
</div>
<table>
    <thead>
        <tr>
            <?php
            $columns_display = ["nom" => "Nom", "pays" => "Pays", "course" => "Course", "temps" => "Temps"];
            foreach ($columns_display as $colKey => $colName) {
                $is_active = ($colKey === $sort);
                $class_asc = ($is_active && $order === 'asc') ? 'active-arrow' : '';
                $class_desc = ($is_active && $order === 'desc') ? 'active-arrow' : '';
 
                echo '<th>';
                echo $colName;
                echo ' <span class="arrows">';
                echo '<a href="?sort=' . $colKey . '&order=asc" class="' . $class_asc . '">&uarr;</a> ';
                echo '<a href="?sort=' . $colKey . '&order=desc" class="' . $class_desc . '">&darr;</a>';
                echo '</span>';
                echo '</th>';
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['nom']) ?></td>
                <td><?= htmlspecialchars($row['pays']) ?></td>
                <td><?= htmlspecialchars($row['course']) ?></td>
                <td><?= htmlspecialchars($row['temps']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
 