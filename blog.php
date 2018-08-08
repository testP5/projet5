<?php
  require("functions/dbAccess.php");// Connexion à la base de données
  require("functions/tools.php");
  
  $postsResume = '';
  
  // Récupération du resumé
  $reponse = $bdd->query('
    SELECT `id`, `title`, `summary`, `idAuthor`, DATE_FORMAT(creationDate, "%d/%m/%Y") AS creationDate 
    FROM `post` ORDER BY id DESC');
  while ($data = $reponse->fetch()){ 
    $postsResume .= '<h3><a href="post.php?id='.$data['id'].'">'.$data['title']."<a></h3>";
    $postsResume .= '<p class="postSummary">'.$data['summary'].'</p><br/>';
    $postsResume .= '<p class="lire"><a href="post.php?id='.$data['id'].'">Lire la suite</a></p>';
    $postsResume .= '<p class="meta">Dernière modification le '.$data['creationDate'].' par <a href="about.php?id='.$data['idAuthor'].'">'.getUserName($data['idAuthor']).'</a></p>';
    $nComments = $bdd->prepare('SELECT COUNT(*) as nbr FROM `comment` WHERE postID = :id');
    $nComments -> execute( array('id' => $data['id'] ));
    while ($count = $nComments->fetch()){
     $postsResume .= '<p class="comment">'.$count['nbr']." commentaire";
      if ($count['nbr']> 1) $postsResume.= "s";
      $postsResume .= '<br/><hr class="souligne">';
    }
    $nComments->closeCursor(); // Termine le traitement de la requête
  }  
  $reponse->closeCursor(); // Termine le traitement de la requête
  ?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php require_once("./fragments/head.html");?>
    <title>Mes articles | GHK P5 Blog - Development PHP</title>
  </head>
  <body>
    <!-- Navigation -->
    <?php require_once("./fragments/nav.html");?>
    <!-- Page Header -->
    <header class="masthead" style="background-image: url('img/blog.jpg')" id="haut">
      <div class="overlay"></div>
      <div class="container">
        <div class="row">
          <div class="col-lg-8 col-md-10 mx-auto">
            <div class="site-heading">
            </div>
          </div>
        </div>
      </div>
    </header>
    <!-- Main Content -->
    <div class="container">
      <a id="haut"></a>
      <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
          <article class="post-preview">
            <?php echo esc_html($postsResume); ?>
          </article>
          <a href="#haut">haut de la page</a><br/>
        </div>
      </div>
    </div>
    <!-- Footer -->
    <?php require_once("./fragments/footer.html");?>
  </body>
</html>