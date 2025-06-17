<?php
// filepath: c:\wamp64\www\ecommerce website\admin\register_admin.php
include '../components/connect.php';

session_start();

$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : '';

if(!$admin_id){
   header('location:admin_login.php');
   exit;
}

$message = [];

if(isset($_POST['submit'])){

   $name = htmlspecialchars(trim($_POST['name']));
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   if(!$name || !$pass || !$cpass){
      $message[] = 'Tous les champs sont obligatoires !';
   } elseif($pass !== $cpass){
      $message[] = 'Les mots de passe ne correspondent pas !';
   } else {
      $select_admin = $conn->prepare("SELECT * FROM admins WHERE name = ?");
      $select_admin->execute([$name]);

      if($select_admin->rowCount() > 0){
         $message[] = 'Le nom d’utilisateur existe déjà !';
      } else {
         $hash = password_hash($pass, PASSWORD_DEFAULT);
         $insert_admin = $conn->prepare("INSERT INTO admins (name, password) VALUES (?, ?)");
         $insert_admin->execute([$name, $hash]);
         $message[] = 'Nouvel admin enregistré avec succès !';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Inscrire un admin</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">
   <form action="" method="post" autocomplete="off">
      <h3>Inscription</h3>
      <?php
      if (!empty($message) && is_array($message)) {
         foreach ($message as $msg) {
            echo '<div class="message">'.htmlspecialchars($msg).'</div>';
         }
      }
      ?>
      <input type="text" name="name" required placeholder="entrer ton nom" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="off">
      <input type="password" name="pass" required placeholder="entrer ton mdp" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="off">
      <input type="password" name="cpass" required placeholder="confirmer ton mdp" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="off">
      <input type="submit" value="Inscription" class="btn" name="submit">
   </form>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>