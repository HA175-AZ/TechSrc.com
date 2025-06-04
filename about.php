<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}
;

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>A propos</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="about">

      <div class="row">

         <div class="image">
            <img src="images/about-img.svg" alt="">
         </div>

         <div class="content">
            <h3> Pourquoi nous choisir ?</h3>
            <p>Cher client,
               Nous vous remercions de votre visite sur notre site Web de Tech et de l’intérêt que
               vous portez à nos produits. Nous apprécions votre temps et sommes là pour vous aider de toutes
               les manières possibles. Dans ce message, nous aimerions nous présenter, expliquer pourquoi vous
               devriez nous choisir et vous fournir les coordonnées pour toute demande ou assistance dont vous pourriez
               avoir besoin.Si vous avez des questions, si vous avez besoin d’aide ou si vous souhaitez nous faire part
               de vos commentaires, n’hésitez pas à nous contacter. Nous sommes là pour vous aider !</p>
            <a href="contact.php" class="btn">Contactez-nous !</a>
         </div>

      </div>

   </section>






   <?php include 'components/footer.php'; ?>

   <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

   <script src="js/script.js"></script>

   <script>

      var swiper = new Swiper(".reviews-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
         breakpoints: {
            0: {
               slidesPerView: 1,
            },
            768: {
               slidesPerView: 2,
            },
            991: {
               slidesPerView: 3,
            },
         },
      });

   </script>

</body>

</html>