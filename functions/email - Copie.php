<?php

function cleanFormInput($str){
  $str = preg_replace('#(<|>)#', '-', $str);  
  $str = str_replace('"', "'",$str);
  $str = str_replace('&', 'et',$str);
  $str = stripslashes(htmlspecialchars($str));
  $str = strip_tags(htmlspecialchars($str));
  $str = trim($str);
  return $str;
}

function contactForm($anchor){
  if ( ! isset($_POST['message']) ) {
    $change = array(
      "{{ anchor }}"  => $anchor,
      "{{ email }}"   => "",
      "{{ message }}" => "",
      "{{ applicant }}"   => "",
      "{{ url }}"     => $_SERVER['PHP_SELF']
    );
    return str_replace(array_keys($change), $change, file_get_contents("fragments/contactForm.html"));
  }

  $adminMail  = "karine0204@gmail.com";
  $check      = '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#';
  $message    = cleanFormInput($_POST['message']);
  $applicant  = cleanFormInput($_POST['applicant']);
  $senderMail = cleanFormInput($_POST['votremail']); 
  $serverMail = "office@42tea.io";
  $str        = "";

  // vérification que les champs sont correctement remplis
  if ( empty($senderMail)            ) $str .= "<li>Vous n'avez pas saisi d'email</li>";
  if (!preg_match($check,$senderMail)) $str .= "<li>l'email n'est pas valide</li>";
  if ( empty($applicant)             ) $str .= "<li>Vous n'avez pas saisi votre nom'</li>";
  if ( empty($message)               ) $str .= "<li>Vous n'avez pas saisi de message</li>";
  if (!checkdnsrr(preg_replace('#[^@]+@(.+)#','$1',$senderMail),'MX')) $str .= "<li>Le nom de domaine de l'adresse e-mail que vous avez donné n'existe pas.</li>"; 

  // des champs ne sont pas correctement saisis
  if ( !empty($str) ) {
    $change = array(
      "{{ anchor }}"    => $anchor,
      "{{ email }}"     => $_POST['votremail'],
      "{{ message }}"   => $_POST['message'],
      "{{ applicant }}" => $_POST['applicant'],
      "{{ url }}"       => $_SERVER['PHP_SELF']
    );
    return str_replace(array_keys($change), $change, file_get_contents("fragments/contactForm.html")).'<div class="formError"><ul>'.$str.'</ul></div>';
  }

  $headers  = "From: " . $serverMail . "\r\n";
  $headers .= "Reply-To: ". $senderMail . "\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=utf8\r\n";

  $change = array(
      "{{ sender }}"     => $applicant,
      "{{ mailSender }}" => $senderMail,
      "{{ message }}"    => $message
    );

  $htmlMessage = str_replace(array_keys($change), $change, file_get_contents("./fragments/templateMail.html"));

  // envoi
  if( mail($adminMail, '[KG blog] vous avez été contacté par : '.$applicant, $htmlMessage, $headers) ) {
    return '<div class="formSuccess"><p>Votre message a bien été envoyé. Merci.</p></div>';
  }  
  else return '<div class="formError"><p>Un problème est survenu durant l\'envoi du mail.</p></div>';
}
?>