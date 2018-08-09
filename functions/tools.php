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

// function esc_html($str){
//   // echo xssafe($str); 
//   echo $str; 
// }

// function xssafe($str){
//   return htmlspecialchars($str,ENT_DISALLOWED,'UTF-8');
// }


//xss mitigation functions
function esc_html($data,$encoding='UTF-8') {
  // echo $data;
  // $data = strip_tags ( $data, [] );
  // $data = htmlspecialchars($data,ENT_QUOTES | ENT_HTML401,$encoding);
  // $data = htmlspecialchars($data,ENT_DISALLOWED,'UTF-8');
  // $data = htmlspecialchars(html_entity_decode($data), ENT_HTML5 | ENT_COMPAT );

//   $data = htmlspecialchars(html_entity_decode($data), ENT_DISALLOWED, 'UTF-8' );
// $data = html_entity_decode($data);

  $data = htmlspecialchars_decode($data, ENT_HTML5 | ENT_COMPAT );

  echo $data;
}

?>