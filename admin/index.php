
<?php

/*************************************************************************************************
    ____  ______   ____                   ________                                               
   / __ \/ ____/  / __ \____  ___  ____  / ____/ /___ ____________________  ____  ____ ___  _____
  / /_/ /___ \   / / / / __ \/ _ \/ __ \/ /   / / __ `/ ___/ ___/ ___/ __ \/ __ \/ __ `__ \/ ___/
 / ____/___/ /  / /_/ / /_/ /  __/ / / / /___/ / /_/ (__  |__  ) /  / /_/ / /_/ / / / / / (__  ) 
/_/   /_____/   \____/ .___/\___/_/ /_/\____/_/\__,_/____/____/_/   \____/\____/_/ /_/ /_/____/  
                    /_/                                                                          
*************************************************************************************************/

require("../functions/dbAccess.php");      // base de donnée + ouverture session
require("adminFunctions.php");   // fonctions et classes nécessaires pour afficher les pages
require("../functions/tools.php");         // nom d'utilisateur

// on regarde si il n'y a pas une session en cours
if( empty ( $_SESSION['id_user'] ) ) {
  if ( isset($_GET['action']) ){
    if ( $_GET['action'] == "login" ) login();
  }
  else {
    $page->showPage( "login", array( "{{ errors }}" => "" ) );
  }
}
else { // il y a une session on regarde si elle est encore active
  if ($_SESSION['last_login'] + (24 * 60 * 60) > time()) $_SESSION['active'] = true;
  else { 
    logout("votre session a expiré");
  }
}

// on est correctement identifié
if (isset($_GET['action'])) { // on a reçu une action
  if (function_exists($_GET['action'])) {
    $_GET['action']();
  }
  else welcome();     // cette action n'est pas prévue : on affiche la page par défaut 
}
else $page->showPage();       // on affiche la page par défaut 
?>