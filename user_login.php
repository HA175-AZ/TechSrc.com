<?php
// filepath: c:\wamp64\www\ecommerce website\user_login.php
include 'components/connect.php';
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

$message = [];

if (isset($_POST['submit'])) {
   $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
   $pass = $_POST['pass'];

   if (!$email || !$pass) {
      $message[] = 'Tous les champs sont obligatoires.';
   } else {
      $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
      $stmt->execute([$email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user) {
         if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('location:index.php');
            exit;
         } else {
            $message[] = 'Mot de passe incorrect.';
         }
      } else {
         $message[] = 'Email inconnu.';
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
   <title>Connexion</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Connexion</h3>
      <?php
      if (!empty($message) && is_array($message)) {
         foreach ($message as $msg) {
            echo '<div class="message">'.$msg.'</div>';
         }
      }
      ?>
      <input type="email" name="email" required placeholder="Entrez votre email" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Entrez votre mot de passe" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Se connecter" class="btn" name="submit">
      <p>Vous n'avez pas de compte ?</p>
      <a href="user_register.php" class="option-btn">Inscrivez-vous !</a>
   </form>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>