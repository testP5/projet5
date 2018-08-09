<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php 
      require_once("./functions/tools.php");
      require_once("./fragments/head.html");
    ?>
    <title>GHK P5 Blog - Developpement PHP</title>
  </head>
  <body>
    <?php require_once("./fragments/nav.html");?>
    <!-- Page Header -->
    <header class="masthead" style="background-image: url('img/index.jpg')" id="haut">
      <div class="overlay"></div>
      <div class="container">
        <div class="row">
          <div class="col-lg-8 col-md-10 mx-auto">
            <div class="site-heading">
             <span class="subheading css-text-shadow">il est temps de developper vos idées ensemble !!</span>
             <div class="logoKarine logoCentral"></div>
            </div>           
          </div>
        </div>
      </div>
    </header>
    <div class="container">
      <p class="index_welcome">Bonjour ! Bienvenue sur mon blog.</p>
      <div class="row" id="a_propos">
        <div class="col-lg-8 col-md-10 mx-auto">
          <img class="img-fluid" src="img/child1.jpg">                 
          <h3>Laissez moi me présenter</h3>
          <p>Passionnée d'Internet et des nouvelles technologies, je travaille en tant qu'expert Web / architecte technique sur les technologies PHP, Symfony.
            Initialement créé pour partager des astuces / et aider les internautes dans plusieurs domaines, ce blog est devenu peu à peu ma carte de visite.
          </p>
          <p>Ma ligne éditoriale vise vraiment à aider et à détailler mes solutions pour aider le plus grand nombre. Si vous avez un projet personnel sur lequel vous aimeriez l’aide d’un développeur, n’hésitez pas à me contacter.</p>
          <p> Consultez mon CV ! : <a href= 'documents/CvGieudesKarine.pdf' target="_blank">par ici (PDF)</a><br/></p>          
          <hr>
        </div>
      </div>
      <div class="row" id="contact">
        <div class="col-lg-8 col-md-10 mx-auto">
          <img class="img-fluid" src="img/contact1.jpg">
          <h3>Laissez vos coordonées afin que je puisse vous contacter !</h3>
          <br/>
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
    <?php require_once("./fragments/footer.html");?>
  </body>
</html>