<?php
// filepath: c:\wamp64\www\ecommerce website\user_register.php
include 'components/connect.php';
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

$message = []; // Toujours un tableau

if (isset($_POST['submit'])) {
   $name = htmlspecialchars(trim($_POST['name']));
   $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   if (!$name || !$email || !$pass || !$cpass) {
      $message[] = 'Tous les champs sont obligatoires.';
   } elseif ($pass !== $cpass) {
      $message[] = 'Les mots de passe ne correspondent pas.';
   } else {
      $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
      $check->execute([$email]);
      if ($check->rowCount() > 0) {
         $message[] = 'Cet email est déjà utilisé.';
      } else {
         $hash = password_hash($pass, PASSWORD_DEFAULT);
         $insert = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
         $insert->execute([$name, $email, $hash]);
         $message[] = 'Inscription réussie !';
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
   <title>Inscription</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Inscription</h3>
      <?php
      if (!empty($message) && is_array($message)) {
         foreach ($message as $msg) {
            echo '<div class="message">'.$msg.'</div>';
         }
      }
      ?>
      <input type="text" name="name" required placeholder="Entrez votre nom" maxlength="20" class="box">
      <input type="email" name="email" required placeholder="Entrez votre email" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Entrez votre mot de passe" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Confirmer votre mot de passe" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="S'inscrire" class="btn" name="submit">
      <p>Vous avez déjà un compte ?</p>
      <a href="user_login.php" class="option-btn">Connectez-vous !</a>
   </form>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>