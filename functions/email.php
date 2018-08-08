<?php

class contactForm {
  private $_anchor;
  private $_adminMail  = "karine0204@gmail.com";
  private $_applicant;
  private $_message;
  private $_senderMail;
  private $_serverMail = "office@42tea.io";

  function __construct($anchor) {
    $this -> _anchor = $anchor ; 
    if ( ! isset($_POST['message']) ) return $this -> newContactForm();// on a besoin d'un formulaire vide

    $this -> _message    = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    $this -> _applicant  = filter_input(INPUT_POST, 'applicant', FILTER_SANITIZE_STRING);
    $this -> _senderMail = filter_input(INPUT_POST, 'votremail', FILTER_SANITIZE_EMAIL);

    $err = $this -> getErrors(); // vérification que les champs sont correctement remplis

    if ( !empty($err) )  return $this -> showActualContactForm($err); // des champs ne sont pas correctement saisis

    $headers  = "From: " . $this -> _serverMail . "\r\n";
    $headers .= "Reply-To: ". $this -> _senderMail . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf8\r\n";

    $change = array(
        "{{ sender }}"     => $this -> _applicant,
        "{{ mailSender }}" => $this -> _senderMail,
        "{{ message }}"    => $this -> _message
      );

    $htmlMessage = str_replace(array_keys($change), $change, file_get_contents("./fragments/templateMail.html"));

    // envoi
    if( mail($this -> _adminMail, '[KG blog] vous avez été contacté par : '.$this -> _applicant, $htmlMessage, $headers) ) {
      echo '<div class="formSuccess"><p>Votre message a bien été envoyé. Merci.</p></div>';
    }  
    else echo '<div class="formError"><p>Un problème est survenu durant l\'envoi du mail.</p></div>';
  }

  /*function cleanFormInput($str){ //nettoye les champs de saisie (texte)
    $str = preg_replace('#(<|>)#', '-', $str);  
    $str = str_replace('"', "'",$str);
    $str = str_replace('&', 'et',$str);
    $str = strip_tags($str);
    $str = trim($str);
    $str = stripslashes($str);
    $str = htmlspecialchars($str);
    return $str;
  }*/

  function getErrors(){  //détermine si il y a des erreurs ou non
    $err = "";
    if ( empty($this -> _senderMail) ) $err .= "<li>Vous n'avez pas saisi d'email</li>";
    if ( empty($this -> _applicant)  ) $err .= "<li>Vous n'avez pas saisi votre nom'</li>";
    if ( empty($this -> _message)    ) $err .= "<li>Vous n'avez pas saisi de message</li>";
    if (!checkdnsrr(preg_replace('#[^@]+@(.+)#','$1',$this -> _senderMail),'MX')) $err .= "<li>Le nom de domaine de l'adresse e-mail que vous avez donné n'existe pas.</li>"; 
    if (!preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#',$this -> _senderMail)) $err .= "<li>l'email n'est pas valide</li>";
    return $err;
  }

  function newContactForm(){  //affiche un formulaire vide
    $change = array(
      "{{ anchor }}"    => $this -> _anchor,
      "{{ email }}"     => "",
      "{{ message }}"   => "",
      "{{ applicant }}" => "",
      "{{ url }}"       => htmlspecialchars($_SERVER['PHP_SELF'])
    );
    echo str_replace(array_keys($change), $change, file_get_contents("fragments/contactForm.html"));
  }

  function showActualContactForm($err){  //retourne le formulaire tel qu'il a été saisi avec un message d'erreur
    $change = array(
      "{{ anchor }}"    => $this -> _anchor,
      "{{ email }}"     => $_POST['votremail'],
      "{{ message }}"   => $_POST['message'],
      "{{ applicant }}" => $_POST['applicant'],
      "{{ url }}"       => htmlspecialchars($_SERVER['PHP_SELF'])
    );
    echo esc_html(str_replace(array_keys($change), $change, file_get_contents("fragments/contactForm.html")));
    echo '<div class="formError"><ul>'.$err.'</ul></div>';
  }
}

?>