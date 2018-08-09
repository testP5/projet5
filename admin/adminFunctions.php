<?php
$page = new Page();
// require_once("../functions/tools.php");

/****************************
          classes
****************************/
class Page {
  private $_defaultPage = "main";  //par défaut on veut afficher la page main
  public function showPage( $toShow = null, $change = null) {
    global $bdd;
    // global xecho;
    if ( is_null($toShow) ) $toShow = $this->_defaultPage;
    if ( is_null($change) ) $change = array();
    if ( $toShow != "login" ) {
      $change = array_merge(
        $change,
        array(
          "{{ userName }}"  => $_SESSION['username'],
          "{{ groupName }}" => $_SESSION['groupName']
        )
      );
    };
    esc_html (str_replace(array_keys($change), $change, file_get_contents("pages/$toShow.html")));
    $bdd = null;                   //fermeture de la connexion
    exit();                        //termine le script php
  }
}


/****************************
        fonctions
****************************/
function addPost(){
  global $page;
  $change = array(
    '{{ id }}'       => $_GET['id'],
    '{{ title }}'    => "",
    '{{ content }}'  => "",
    '{{ summary }}'  => "",
    '{{ url }}'      => strtok($_SERVER["REQUEST_URI"],'?'),
    '{{ action }}'   => "savePost"
  );
  $chaine = str_replace(array_keys($change), $change, file_get_contents("pages/fragments/editPost.html"));
  $page->showPage(null, array("{{ page }}" => $chaine));
}

function deleteComment(){
global $bdd, $page;
  $delComment = $bdd -> prepare('DELETE FROM comment WHERE commentID=:id');
  $delComment -> execute( array( "id"=> filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ) );
  $delComment -> closeCursor();
  $page->showPage(null, array("{{ page }}" => 'Ce commentaire est supprimé'));
}

function editPost(){
  global $bdd, $page;
  $chaine = "";
  try {
    $reqc = $bdd->prepare('SELECT  `id`, `title`, `content`, `idAuthor`, `summary` FROM `post` WHERE `id`=:id'); 
    $reqc -> execute( array( "id"=> filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ) );
  }
  catch(Exception $err) {
    die('<b>Catched exception at line '. $err->getLine() .' :</b> '. $err->getMessage());
  }

  while ($data = $reqc ->fetch()){
    $change = array(
      '{{ id }}'       => $data['id'] ,
      '{{ title }}'    => $data['title'],
      '{{ content }}'  => $data['content'],
      '{{ idAuthor }}' => $data['idAuthor'],
      '{{ summary }}'  => $data['summary'],
      '{{ url }}'      => strtok($_SERVER["REQUEST_URI"],'?'),
      '{{ action }}'   => "updatePost"
    );
    $chaine .= str_replace(array_keys($change), $change, file_get_contents("pages/fragments/editPost.html"));
  }
  $reqc -> closeCursor();
  $page->showPage(null, array("{{ page }}" => $chaine));
}

function deletePost(){
  global $bdd, $page;
  $delPost = $bdd->prepare('DELETE FROM `post` WHERE `id` =:id');
  $delPost -> execute( array( "id"=> filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ) );
  $delPost -> closeCursor();
  $page->showPage(null, array("{{ page }}" => 'Post correctement supprimé'));
}

function listComments(){
  global $bdd, $page;
  $chaine = "";
  try {
    $reqc = $bdd->prepare('SELECT `commentID`,`name`,`comment`, DATE_FORMAT(commentDate, "%d/%m/%Y") AS commentDate FROM `comment`  ORDER BY `commentDate`'); //requete SQL
    $reqc -> execute();
  }
  catch(Exception $err) {
    exit('<b>Catched exception at line '. $err->getLine() .' :</b> '. $err->getMessage());
  }

  while ($data = $reqc ->fetch()){
    $change = array(
      '{{ comment }}'     => $data['comment'] ,
      '{{ commentDate }}' => $data['commentDate'],
      '{{ commentID }}'   => $data['commentID'],
      '{{ name }}'        => $data['name']
    );
    $chaine .= str_replace(array_keys($change), $change, file_get_contents("pages/fragments/listComment.html"));
  }
  $page->showPage(null, array("{{ page }}" => $chaine));
}

function listPosts(){
  global $bdd, $page;
  $chaine = "";
  $index  = 0;
  try {
  $reqc = $bdd->prepare('SELECT id, title, idAuthor, summary, creationDate, DATE_FORMAT(creationDate, \'%d/%m/%Y à %Hh%imin%ss\') AS date_creation_fr FROM `post` ORDER BY id DESC');//requete SQL
      $reqc -> execute();
  }
  catch(Exception $err) {
    exit('<b>Catched exception at line '. $err->getLine() .' :</b> '. $err->getMessage());
  }

  while ($data = $reqc ->fetch()){
    $change = array(
      '{{ id }}'     => $data['id'] ,
      '{{ title }}' => $data['title'],
      '{{ idAuthor }}'   => getUserName($data['idAuthor']),
      '{{ content }}'        => $data['summary'],
      '{{ creationDate }}' =>$data['creationDate']
    );
    $chaine .= str_replace(array_keys($change), $change, file_get_contents("pages/fragments/listPost.html"));
    $index++;
  }
  $index++;
  $chaine .= '<h5><a href="index.php?action=addPost&id='.$index.'"><button>Ajouter un article</button></a></h5>';

  $page->showPage(null, array("{{ page }}" => $chaine)); 
}

function login(){
  global $bdd, $page;
  if (isset( $_SESSION['active'] ) ){
    if ( $_SESSION['active'] ) welcome();            //on affiche la page par défaut 
  }
  try {
    $reqc = $bdd->prepare('
      SELECT `userID`, `firstName`, `lastName`, `password`, `role`, `active`, `lastLogin` 
      FROM `user` 
      WHERE `login`=:username'
    );
    $reqc -> execute( array( "username"=> filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) ) );
    $data = $reqc ->fetchAll(PDO::FETCH_OBJ);
  }
  catch(Exception $err) {
    die('<b>Catched exception at line '. $err->getLine() .' :</b> '. $err->getMessage());
  }
  if (count($data) == 0) {                          //la requète n'a pas aboutie (mauvais utilisateur)
    $page->showPage("login", array( "{{ errors }}" => "utilisateur ou mot de passe incorrect :(" ));
  }
  else {                                            // la requète à aboutie
    $data = get_object_vars($data[0]);
    if ($data['password'] != filter_input(INPUT_POST, 'password', FILTER_DEFAULT)) {  // mauvais mot de passe
      $page->showPage("login", array( "{{ errors }}" => "utilisateur ou mot de passe incorrect :(" ));
    }
    if ($data['active'] != 1) {                     // inactif
      $page->showPage("login", array( "{{ errors }}" => "Votre compte n'est pas actif :/" ));
    }
    //on enregistre les variables de sessions
    $_SESSION['active']     = true;
    $_SESSION['groupName']  = $data['role'];
    $_SESSION['last_login'] = time();
    $_SESSION['userID']     = $data['userID'];
    $_SESSION['username']   = $data['firstName']." ".$data['lastName'];
    $reqs = $bdd->prepare('UPDATE `user` SET `lastLogin` = ":now" WHERE `user`.`userID` = :userID');
    $reqs -> execute( array( 
      "now"    => date( "y-m-d H:i:s", time() ),
      "userID" => $_SESSION['userID']
      ) );
    $reqs -> closeCursor();
    welcome();                                    //on affiche la page par défaut 
  }
}

function logout($msg=null){
  global $page;
  $_SESSION = array();
  session_destroy();
  unset($_SESSION);
  if ( is_null($msg) ) $msg = "";
  $page->showPage("login", array( "{{ errors }}" => $msg ));
}

function memberList(){
  global $bdd, $page;
  $chaine = "";
  try {
  $reqc = $bdd->prepare('SELECT firstName, lastName, email, role FROM `user`');
      $reqc -> execute();
  }
  catch(Exception $err) {
    exit('<b>Catched exception at line '. $err->getLine() .' :</b> '. $err->getMessage());
  }

  while ($data = $reqc ->fetch()){
    $change = array(
      '{{ firstName }}'     => $data['firstName'] ,
      '{{ lastName }}'      => $data['lastName'],
      '{{ email }}'         => $data['email'],
      '{{ role }}'           => $data['role']

    );
  $chaine .= str_replace(array_keys($change), $change, '<table><tr><td>{{ firstName }} {{ lastName }}</td><td>{{ email }}</td><td>{{ role }}</td></table><hr>');
  }
  return $chaine;
}

function savePost(){
 global $bdd, $page;
  try {
    $reqc = $bdd->prepare('INSERT INTO post ( id, title, content, summary, idAuthor, creationDate, lastModification) VALUES (:id, :title, :content, :summary, :idAuthor, :creationDate, :creationDate)');
    $reqc -> execute(
      array(
        'id'           => filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT),
        'idAuthor'     => $_SESSION['userID'],
        'title'        => filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING),
        'content'      => filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        'summary'      => filter_input(INPUT_POST, 'resume', FILTER_SANITIZE_STRING),
        'creationDate' => date('Y-m-d')
      )
    );
  }
  catch(Exception $err) {
    exit('<b>Catched exception at line '. $err->getLine() .' :</b> '. $err->getMessage());
  }
  $page->showPage(null, array("{{ page }}" => 'Post enregistré et publié :)'));
}

function updatePost(){
 global $bdd, $page;
  try {
    $reqU = $bdd->prepare('UPDATE `post` SET `title`= ?  , `summary`=  ? , `lastModification`=?, `content`=?   WHERE id=?');
    $reqU-> execute(
      array(
        filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING),
        filter_input(INPUT_POST, 'resume', FILTER_SANITIZE_STRING),
        date('Y-m-d'),
        filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING),
        filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)
      )
    );
  }
  catch(Exception $err) {
    exit('<b>Catched exception at line '. $err->getLine() .' :</b> '. $err->getMessage());
  }
  $page->showPage(null, array("{{ page }}" => 'Post enregistré et publié :)'));
}

function validateComment(){
}

function welcome(){
  global $page;
  $page->showPage(null, array("{{ page }}" => "Bienvenue ".$_SESSION['username'].", en tant que membre vous pouvez désormais rajouter des articles et gérer les commentaires.<br/><P><U>Les membres déjà inscrits :</U><hr></P>".memberList()));
}
 
?>