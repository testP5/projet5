<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN" "[http://www.w3.org/TR/html4/strict.dtd]">  
<html lang="fr">
  <head>
    <head>
      <?php require_once("./fragments/head.html");?>
      <title>GHK P5 Blog - Development PHP -</title>
  </head>
  <body style="direction: ltr;">
    <!-- Navigation -->
    <?php require_once("./fragments/nav.html");?>
    <!-- Page Header -->
    <header class="masthead" style="background-image: url('img/contact.jpg')" id="haut">
      <div class="overlay"></div>
      <div class="container">
        <div class="row">
          <div class="col-lg-8 col-md-10 mx-auto">
            <div class="page-heading">
              <h2>Contactez moi</h2>
            </div>
          </div>
        </div>
      </div>
    </header>
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
          <h3>Laissez vos coordonées afin que je puisse vous contacter !</h3>
          <div id="contactForm">
            <?php 
              require_once("./functions/email.php");
              new contactForm("contactForm");
              ?>
          </div>
          <a href="#haut">haut de la page</a><br/>
        </div>
      </div>
    </div>
    <!-- Footer -->
    <?php require_once("./fragments/footer.html");?>
  </body>
</html>