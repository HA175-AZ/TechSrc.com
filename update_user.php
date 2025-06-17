<?php
// filepath: c:\wamp64\www\ecommerce website\update_profile.php
include 'components/connect.php';
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

$message = [];

if ($user_id == '') {
   header('location:user_login.php');
   exit;
}

// Récupérer les infos actuelles de l'utilisateur
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
   $name = htmlspecialchars(trim($_POST['name']));
   $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

   // Si l'utilisateur veut changer son mot de passe
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   if (!$name || !$email) {
      $message[] = 'Nom et email sont obligatoires.';
   } else {
      // Vérifier si l'email est déjà utilisé par un autre utilisateur
      $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
      $check->execute([$email, $user_id]);
      if ($check->rowCount() > 0) {
         $message[] = 'Cet email est déjà utilisé par un autre compte.';
      } else {
         // Mise à jour du nom et de l'email
         $update = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
         $update->execute([$name, $email, $user_id]);
         $message[] = 'Profil mis à jour !';

         // Si les champs mot de passe sont remplis, on vérifie et on met à jour
         if (!empty($pass) || !empty($cpass)) {
            if ($pass !== $cpass) {
               $message[] = 'Les mots de passe ne correspondent pas.';
            } else {
               $hash = password_hash($pass, PASSWORD_DEFAULT);
               $update_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
               $update_pass->execute([$hash, $user_id]);
               $message[] = 'Mot de passe mis à jour !';
            }
         }
         // Rafraîchir les infos utilisateur
         $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
         $stmt->execute([$user_id]);
         $user = $stmt->fetch(PDO::FETCH_ASSOC);
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
   <title>Modifier le profil</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Modifier le profil</h3>
      <?php
      if (!empty($message) && is_array($message)) {
         foreach ($message as $msg) {
            echo '<div class="message">'.$msg.'</div>';
         }
      }
      ?>
      <input type="text" name="name" required placeholder="Votre nom" maxlength="20" class="box" value="<?= htmlspecialchars($user['name']) ?>">
      <input type="email" name="email" required placeholder="Votre email" maxlength="50" class="box" value="<?= htmlspecialchars($user['email']) ?>" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" placeholder="Nouveau mot de passe (laisser vide pour ne pas changer)" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" placeholder="Confirmer le nouveau mot de passe" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Mettre à jour" class="btn" name="submit">
      <a href="index.php" class="option-btn">Retour accueil</a>
   </form>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>