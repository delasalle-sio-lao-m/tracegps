<?php
// Projet TraceGPS
// fichier : modele/DAO.test1.php
// Rôle : test de la classe DAO.class.php
// Dernière mise à jour : 13/10/2020 par Jeremy Tcha

// Le code des tests restant à développer va être réparti entre les membres de l'équipe de développement.
// Afin de limiter les conflits avec GitHub, il est décidé d'attribuer un fichier de test à chaque développeur.
// Développeur 1 : fichier DAO.test1.php
// Développeur 2 : fichier DAO.test2.php
// Développeur 3 : fichier DAO.test3.php
// Développeur 4 : fichier DAO.test4.php

// Quelques conseils pour le travail collaboratif :
// avant d'attaquer un cycle de développement (début de séance, nouvelle méthode, ...), faites un Pull pour récupérer
// la dernière version du fichier.
// Après avoir testé et validé une méthode, faites un commit et un push pour transmettre cette version aux autres développeurs.
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Test de la classe DAO</title>
	<style type="text/css">body {font-family: Arial, Helvetica, sans-serif; font-size: small;}</style>
</head>
<body>

<?php
// connexion du serveur web à la base MySQL
include_once ('DAO.class.php');
$dao = new DAO();


// test de la méthode xxxxxxxxxxxxxxxxxxxxxxxxxxx ----------------------------------------------------------
// modifié par xxxxxxxxxxxxxxxxx le xxxxxxxxxx
echo "<h3>Test de Jérémy : </h3>";


// test de la méthode autoriseAConsulter ----------------------------------------------------------
// modifié par Jim le 13/8/2018
echo "<h3>Test de autoriseAConsulter : </h3>";
if ($dao->autoriseAConsulter(2, 3)) $autorise = "oui"; else $autorise = "non";
echo "<p>L'utilisateur 2 autorise l'utilisateur 3 : <b>" . $autorise . "</b><br>";

if ($dao->autoriseAConsulter(3, 2)) $autorise = "oui"; else $autorise = "non";
echo "<p>L'utilisateur 3 autorise l'utilisateur 2 : <b>" . $autorise . "</b><br>";


// test de la méthode supprimerUneAutorisation ----------------------------------------------------
// modifié par Jim le 13/8/2018
echo "<h3>Test de supprimerUneAutorisation : </h3>";
// on crée une autorisation
if ($dao->creerUneAutorisation(2, 1)) $ok = "oui"; else $ok = "non";
echo "<p>La création de l'autorisation de l'utilisateur 2 vers l'utilisateur 1 a réussi : <b>" . $ok . "</b><br>";
// puis on la supprime
if ($dao->supprimerUneAutorisation(2, 1)) $ok = "oui"; else $ok = "non";
echo "<p>La suppression de l'autorisation de l'utilisateur 2 vers l'utilisateur 1 a réussi : <b>" . $ok . "</b><br>";


// test de la méthode getToutesLesTraces ----------------------------------------------------------
// modifié par Jim le 14/8/2018
echo "<h3>Test de getToutesLesTraces : </h3>";
$lesTraces = $dao->getToutesLesTraces();
$nbReponses = sizeof($lesTraces);
echo "<p>Nombre de traces : " . $nbReponses . "</p>";
// affichage des traces
foreach ($lesTraces as $uneTrace)
{   echo ($uneTrace->toString());
echo ('<br>');
}


// test de la méthode supprimerUneTrace -----------------------------------------------------------
// modifié par Jim le 15/8/2018
echo "<h3>Test de supprimerUneTrace : </h3>";
$ok = $dao->supprimerUneTrace(22);
if ($ok) {
    echo "<p>Trace bien supprimée !</p>";
}
else {
    echo "<p>Echec lors de la suppression de la trace !</p>";
}


// test des méthodes creerUnPointDeTrace et terminerUneTrace --------------------------------------
// modifié par Jim le 15/8/2018
echo "<h3>Test de terminerUneTrace : </h3>";
// on choisit une trace non terminée
$unIdTrace = 3;
// on l'affiche
$laTrace = $dao->getUneTrace($unIdTrace);
echo "<h4>l'objet laTrace avant l'appel de la méthode terminerUneTrace : </h4>";
echo ($laTrace->toString());
echo ('<br>');
// on la termine
$dao->terminerUneTrace($unIdTrace);
// et on l'affiche à nouveau
$laTrace = $dao->getUneTrace($unIdTrace);
echo "<h4>l'objet laTrace après l'appel de la méthode terminerUneTrace : </h4>";
echo ($laTrace->toString());
echo ('<br>');


// ferme la connexion à MySQL :
unset($dao);
?>

</body>
</html>