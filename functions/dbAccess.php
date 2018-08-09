<?php
// ini_set('display_errors', 0);
session_start();
// Connexion à la base de données
try
{
  $bdd = new PDO('mysql:host=localhost;dbname=teaihnct_opcP5;charset=utf8', 'teaihnct_opcP5', 'j#vm@E#kBWF_', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
}
catch(Exception $err)
{
  die('Erreur : '.esc_html($err->getMessage()));
}
?>