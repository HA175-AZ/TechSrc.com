<?php
// filepath: c:\wamp64\www\ecommerce website\admin\update_admin.php
include '../components/connect.php';

session_start();

$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : '';

if(!$admin_id){
   header('location:admin_login.php');
   exit;
}

// Récupérer les infos actuelles de l'admin
$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$fetch_profile = $stmt->fetch(PDO::FETCH_ASSOC);

$message = [];

if(isset($_POST['submit'])){

   $name = htmlspecialchars(trim($_POST['name']));

   // Mise à jour du nom
   $update_profile_name = $conn->prepare("UPDATE admins SET name = ? WHERE id = ?");
   $update_profile_name->execute([$name, $admin_id]);

   // Gestion du mot de passe
   $old_pass = $_POST['old_pass'];
   $new_pass = $_POST['new_pass'];
   $confirm_pass = $_POST['confirm_pass'];

   if(!empty($old_pass) || !empty($new_pass) || !empty($confirm_pass)){
      if(empty($old_pass)){
         $message[] = 'Veuillez entrer l\'ancien mot de passe !';
      } elseif(!password_verify($old_pass, $fetch_profile['password'])){
         $message[] = 'Ancien mot de passe incorrect !';
      } elseif(empty($new_pass)){
         $message[] = 'Veuillez entrer un nouveau mot de passe !';
      } elseif($new_pass !== $confirm_pass){
         $message[] = 'La confirmation du mot de passe ne correspond pas !';
      } else {
         $hash = password_hash($new_pass, PASSWORD_DEFAULT);
         $update_admin_pass = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
         $update_admin_pass->execute([$hash, $admin_id]);
         $message[] = 'Mot de passe mis à jour avec succès !';
      }
   } else {
      $message[] = 'Profil mis à jour !';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Mettre à jour le profil</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">
   <form action="" method="post" autocomplete="off">
      <h3>Mettre à jour le profil</h3>
      <?php
      if (!empty($message) && is_array($message)) {
         foreach ($message as $msg) {
            echo '<div class="message">'.htmlspecialchars($msg).'</div>';
         }
      }
      ?>
      <input type="text" name="name" value="<?= htmlspecialchars($fetch_profile['name']); ?>" required placeholder="entrer ton nom" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" placeholder="entrer ancien mdp" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="off">
      <input type="password" name="new_pass" placeholder="entrer nouveau mdp" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="off">
      <input type="password" name="confirm_pass" placeholder="confirmer nouveau mdp" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" autocomplete="off">
      <input type="submit" value="Mettre à jour" class="btn" name="submit">
   </form>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>