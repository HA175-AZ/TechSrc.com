<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
   exit;
}

if(isset($_POST['order'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = 'flat no. '. $_POST['flat'] .', '. $_POST['street'] .', '. $_POST['city'] .', '. $_POST['state'] .', '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){

      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);
      $order_id = $conn->lastInsertId(); // Récupère l'id de la commande

      // Insertion des produits commandés dans order_items
      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);
      while($cart_item = $select_cart->fetch(PDO::FETCH_ASSOC)){
         $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
         $stmt->execute([
            $order_id,
            $cart_item['pid'],        // Utilise 'pid' ici
            $cart_item['quantity'],
            $cart_item['price']
         ]);
      }

      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'Commande passée avec succès !';
   }else{
      $message[] = 'Votre panier est vide';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">

   <form action="" method="POST">

   <h3>Votre Commandes</h3>

      <div class="display-orders">
      <?php
         $grand_total = 0;
         $cart_items = [];
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
               $total_products = implode($cart_items);
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
         <p> <?= $fetch_cart['name']; ?> <span>(<?= '$'.$fetch_cart['price'].'/- x '. $fetch_cart['quantity']; ?>)</span> </p>
      <?php
            }
         }else{
            echo '<p class="empty">Votre panier est vide !</p>';
         }
      ?>
         <input type="hidden" name="total_products" value="<?= isset($total_products) ? $total_products : ''; ?>">
         <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
         <div class="grand-total">grand total : <span>$<?= $grand_total; ?></span></div>
      </div>

      <h3>Passez vos commandes</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Votre Nom:</span>
            <input type="text" name="name" placeholder="Entrez votre nom" class="box" maxlength="20" required>
         </div>
         <div class="inputBox">
            <span>Votre numéro :</span>
            <input type="number" name="number" placeholder="Entrez votre numéro" class="box" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;" required>
         </div>
         <div class="inputBox">
            <span>Votre email :</span>
            <input type="email" name="email" placeholder="Entrez votre email" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Méthode paiement :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">Paiement à la livraison</option>
               <option value="credit card">Carte banquaire</option>
               <option value="paytm">Paytm</option>
               <option value="paypal">Paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Ligne d’adresse 01:</span>
            <input type="text" name="flat" placeholder="e.g. Numéro fixe" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Ligne d’adresse  02 :</span>
            <input type="text" name="street" placeholder="e.g. Nom de la rue" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Ville :</span>
            <input type="text" name="city" placeholder="e.g. Chicago" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Etat:</span>
            <input type="text" name="state" placeholder="e.g. illinois " class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Pays :</span>
            <input type="text" name="country" placeholder="e.g. USA" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Code PIN :</span>
            <input type="number" min="0" name="pin_code" placeholder="e.g. 123456" min="0" max="999999" onkeypress="if(this.value.length == 6) return false;" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>" value="Commandes passées">

   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>