<?php
session_start();
 
 
$usersFile = "users.json";
 
 
if (!file_exists($usersFile)) {
    file_put_contents($usersFile, json_encode([]));
}
$users = json_decode(file_get_contents($usersFile), true);
 
 
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
 
 
if (isset($_POST['action']) && $_POST['action'] === "register") {
 
    $username = trim($_POST['register_username']);
    $password = trim($_POST['register_password']);
 
   
    if (empty($username)) {
        $registerError = "Le champ username est vide.";
    } elseif (empty($password)) {
        $registerError = "Le champ password est vide.";
    }
   
    elseif (isset($users[$username])) {
        $registerError = "Ce nom d'utilisateur existe déjà.";
    }
    else {
       
        $users[$username] = password_hash($password, PASSWORD_DEFAULT);
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
 
        $registerSuccess = "Inscription réussie ! Vous pouvez vous connecter.";
    }
}
 
 
if (isset($_POST['action']) && $_POST['action'] === "login") {
 
    $username = trim($_POST['login_username']);
    $password = trim($_POST['login_password']);
 
   
    if (empty($username)) {
        $loginError = "Le champ username est vide.";
    } elseif (empty($password)) {
        $loginError = "Le champ password est vide.";
    }
   
    elseif (!isset($users[$username])) {
        $loginError = "Le nom d'utilisateur n'existe pas.";
    }
   
    elseif (!password_verify($password, $users[$username])) {
        $loginError = "Mot de passe invalide.";
    }
    else {
        $_SESSION['username'] = $username;
    }
}
 
 
if (isset($_SESSION['username'])) {
    echo "<h1>Bonjour " . htmlspecialchars($_SESSION['username']) . "</h1>";
    echo '<a href="?logout=1"><button>Déconnexion</button></a>';
    exit();
}
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Login + Inscription (JSON)</title>
<style>
    body { font-family: Arial; padding: 30px; }
    .box { width: 300px; padding: 20px; border: 1px solid #ccc; margin-bottom: 20px; }
    input { width: 95%; padding: 8px; margin-bottom: 10px; }
    button { padding: 8px 15px; cursor: pointer; }
    .error { color: red; }
    .success { color: green; }
</style>
</head>
<body>
 
 
<div class="box">
<h2>Connexion</h2>
<?php if (isset($loginError)) echo "<p class='error'>$loginError</p>"; ?>
<form method="POST">
    <input type="hidden" name="action" value="login">
 
    <label>Nom d'utilisateur :</label>
    <input type="text" name="login_username">
 
    <label>Mot de passe :</label>
    <input type="password" name="login_password">
 
    <button type="submit">Se connecter</button>
</form>
</div>
 
 
<div class="box">
<h2>Inscription</h2>
<?php if (isset($registerError)) echo "<p class='error'>$registerError</p>"; ?>
<?php if (isset($registerSuccess)) echo "<p class='success'>$registerSuccess</p>"; ?>
<form method="POST">
    <input type="hidden" name="action" value="register">
 
    <label>Nom d'utilisateur :</label>
    <input type="text" name="register_username">
 
    <label>Mot de passe :</label>
    <input type="password" name="register_password">
 
    <button type="submit">S'inscrire</button>
</form>
</div>
 
</body>
</html>