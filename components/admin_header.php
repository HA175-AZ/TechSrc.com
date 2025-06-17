<?php
if(isset($message) && is_array($message)){
   foreach($message as $msg){
      echo '
      <div class="message">
         <span>'.htmlspecialchars($msg).'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">

      <a href="../admin/dashboard.php" class="logo">Admin<span>PanelTechsrc.</span></a>

      <nav class="navbar">
         <a href="../admin/dashboard.php">Accueil</a>
         <a href="../admin/products.php">Produits</a>
         <a href="../admin/placed_orders.php">Commandes</a>
         <a href="../admin/admin_accounts.php">Admins</a>
         <a href="../admin/users_accounts.php">Utilisateurs</a>
         <a href="../admin/messages.php">Messages</a>
      </nav>

      <?php if(isset($admin_id) && !empty($admin_id)): ?>
         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
         </div>

         <div class="profile">
            <?php
               $select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
               $select_profile->execute([$admin_id]);
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <?php if ($fetch_profile): ?>
               <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
            <?php else: ?>
               <p class="message">Profil admin introuvable.</p>
            <?php endif; ?>
            <a href="../admin/update_profile.php" class="btn">Mettre à jour le profil</a>
            <div class="flex-btn">
               <a href="../admin/register_admin.php" class="option-btn">Inscription</a>
               <a href="../admin/admin_login.php" class="option-btn">Connexion</a>
            </div>
            <a href="../components/admin_logout.php" class="delete-btn" onclick="return confirm('Se déconnecter du site ?');">Déconnexion</a> 
         </div>
      <?php endif; ?>

   </section>

</header>