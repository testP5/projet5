<?php
function getUserName($user){
  global $bdd;
  $reqUser = $bdd->query("SELECT `firstName` FROM `user` WHERE `userID`=$user");
  while ($dataUser = $reqUser->fetch()){
    $user= $dataUser['firstName'];
  }
  $reqUser->closeCursor();
  return $user;
}

function esc_html($str){
  // echo xssafe($str); 
  echo $str; 
}

function xssafe($str){
  return htmlspecialchars($str,ENT_DISALLOWED,'UTF-8');
}
?>