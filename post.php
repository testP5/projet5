<?php
  require("functions/dbAccess.php");
  
  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  
  require("functions/tools.php");
  
  if (filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING) !== null ) {
    try {
      $reqc = $bdd->prepare('INSERT INTO comment ( `name`, `comment`, `commentDate`, `postID`, `email`) VALUES (:name, :comment, :commentDate, :postID, :email)');
      $reqc->execute(
        array(
          'name'        => filter_input(INPUT_POST, 'author', FILTER_SANITIZE_STRING),
          'comment'     => filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING),
          'commentDate' => date("y-m-d H:i:s",time()),
          'postID'      => filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT),
          'email'       => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
        )
      );
    }
    catch(Exception $e) {
      $newComment = 'Error :<b>Catched exception at line '. $e->getLine() .' :</b> '. $e->getMessage();
      $commentClass = "error";
    }
    $newComment = "Votre commentaire a bien été pris en compte :) Merci pour votre participation";
    $commentClass="";
  }
  else $newComment = NULL;

  // Récupération du post
  $reponse = $bdd->prepare('SELECT id, title, idAuthor, content, creationDate, DATE_FORMAT(creationDate, \'%d/%m/%Y à %Hh%imin%ss\') AS date_creation_fr FROM `post` WHERE id = :id');
  $reponse -> execute( array( 'id' => filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ));

  while ($data = $reponse->fetch()){
    $donnees = $data;
  }
  $reponse->closeCursor(); // Termine le traitement de la requête
  ?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php require_once("./fragments/head.html");?>
    <title><?php echo esc_html($donnees['title']); ?> | Karine G. - Development PHP</title>
  </head>
  <body>
    <!-- Navigation -->
    <?php require_once("./fragments/nav.html");?>
    <!-- Page Header -->
    <header class="masthead" style="background-image: url('img/bureau.jpg')">
      <div class="overlay"></div>
      <div class="container">
        <div class="row">
          <div class="col-lg-8 col-md-10 mx-auto">
            <div class="post-heading">
            </div>
          </div>
        </div>
      </div>
    </header>
    <div class="container">
      <div class="news">
        <?php 
          echo esc_html('<h3>'.htmlspecialchars($donnees['title']).'</h3>    
           <em>le '.$donnees['creationDate'].'</em>
          <p>'.$donnees['content'].'</p>
          <p>Cet article a été écrit par <a href="about.php?id='.$donnees['idAuthor'].'">'.getUserName($donnees['idAuthor']).'</a></p>');
          ?>
        <div class="allComments">
          <h4 id="comments">Commentaires</h4>
          <?php
            if (!is_null($newComment)){
              echo esc_html("<p class='newComment $commentClass'>$newComment</p>");
            }
            
            
            // Récupération des commentaires
            $req = $bdd->prepare('SELECT `name`,`comment`, DATE_FORMAT(commentDate, "%d/%m/%Y") AS commentDate FROM `comment` WHERE `postID`=:id ORDER BY `commentDate`'); 
            $req -> execute( array("id" => filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) );
            
            while ($data = $req->fetch()){
              echo esc_html('<h7>'.$data['name'].' le '.$data['commentDate'].'</h7>
              <p>'.nl2br($data['comment']).'</p>');
              } // Fin de la boucle des commentaires
            $req->closeCursor();
            ?>
        </div>
      </div>
    </div>
    <hr>
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
          <section id="comments" class="comments-area clr">
            <div id="respond" class="comment-respond">
              <h3 id="reply-title" class="comment-reply-title">Laisser un commentaire <small><a rel="nofollow" id="cancel-comment-reply-link" href="/plugins-wordpress/comment-personnaliser-lespace-admin-de-votre-blog-wordpress#respond" style="display:none;"><span class="fa fa-times"></span>Annuler la réponse au commentaire</a></small></h3>
              <form action="post.php?id=<?php echo filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); ?>#comments" method="post" id="commentform" class="comment-form" novalidate="">
                <p class="comment-notes"><span id="email-notes">Votre adresse de messagerie ne sera pas publiée.</span> Les champs obligatoires sont indiqués avec <span class="required">*</span></p>
                <table>
                  <tr>
                    <td class="comment-form-author"><label for="author">Nom <span class="required">*</span></label></td>
                    <td><input id="author" name="author" type="text" value="" size="30" maxlength="245" required="required"></p></td>
                  </tr>
                  <tr>
                    <td class="comment-form-email"><label for="email">Adresse de messagerie <span class="required">*</span></label></td>
                    <td><input id="email" name="email" type="email" value="" size="30" maxlength="100" aria-describedby="email-notes" required="required"></td>
                  </tr>
                  <tr>
                    <td class="comment-form-comment"><label for="comment">Commentaire</label></td>
                    <td><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea></td>
                  </tr>
                </table>
                <p class="form-submit"><input name="submit" type="submit" id="submit" class="submit" value="Envoyer">
                </p>
              </form>
            </div>
          </section>
          <p><a href="blog.php">Retour à la liste des posts</a></p>
        </div>
      </div>
    </div>
    <!-- Footer -->
    <?php require_once("./fragments/footer.html");?> 
  </body>
</html>