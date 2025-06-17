<?php
// filepath: c:\wamp64\www\ecommerce website\admin\admin_login.php
include '../components/connect.php';
session_start();

$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

$message = [];

if (isset($_POST['submit'])) {
   $name = htmlspecialchars(trim($_POST['name']));
   $pass = $_POST['pass'];

   if (!$name || !$pass) {
      $message[] = 'Tous les champs sont obligatoires !';
   } else {
      $stmt = $conn->prepare("SELECT * FROM admins WHERE name = ?");
      $stmt->execute([$name]);
      $admin = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($admin && password_verify($pass, $admin['password'])) {
         session_regenerate_id(true);
         $_SESSION['admin_id'] = $admin['id'];
         header('location:dashboard.php');
         exit;
      } else {
         $message[] = 'Identifiants incorrects.';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Connexion Admin</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<?php include '../components/admin_header.php'; ?>

<section class="form-container">
   <form action="" method="post" autocomplete="off">
      <h3>Connexion Admin</h3>
      <?php
      if (!empty($message) && is_array($message)) {
         foreach ($message as $msg) {
            echo '<div class="message">'.htmlspecialchars($msg).'</div>';
         }
      }
      ?>
      <input type="text" name="name" required placeholder="Nom" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="off">
      <input type="password" name="pass" required placeholder="Mot de passe" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="off">
      <input type="submit" value="Se connecter" class="btn" name="submit">
      <a href="../index.php" class="option-btn">Retour accueil</a>
   </form>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>