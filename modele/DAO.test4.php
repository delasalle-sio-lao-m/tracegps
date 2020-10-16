<?php
// Projet TraceGPS
// fichier : modele/DAO.test1.php
// Rôle : test de la classe DAO.class.php
// Dernière mise à jour : 13/10/2020 par Monorom Lao

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


// // test de la méthode creerUneAutorisation ----------------------------------------------------------
// // modifié par Monorom LAO le 13/10/2020
// echo "<h3>Test de creerUneAutorisation : </h3>";
// if ($dao->creerUneAutorisation(2, 1)) $ok = "oui"; else $ok = "non";
// echo "<p>La création de l'autorisation de l'utilisateur 2 vers l'utilisateur 1 a réussi : <b>" . $ok . "</b><br>";
// // la même autorisation ne peut pas être enregistrée 2 fois
// if ($dao->creerUneAutorisation(2, 1)) $ok = "oui"; else $ok = "non";
// echo "<p>La création de l'autorisation de l'utilisateur 2 vers l'utilisateur 1 a réussi : <b>" . $ok . "</b><br>";


// // test de la méthode getLesPointsDeTrace ---------------------------------------------------------
// // modifié par Jim le 13/8/2018
// echo "<h3>Test de getLesPointsDeTrace : </h3>";
// $lesPoints = $dao->getLesPointsDeTrace(1);
// $nbPoints = sizeof($lesPoints);
// echo "<p>Nombre de points de la trace 1 : " . $nbPoints . "</p>";
// // affichage des points
// foreach ($lesPoints as $unPoint)
// {   echo ($unPoint->toString());
// echo ('<br>');
// }


// test de la méthode getLesTraces($idUtilisateur) ------------------------------------------------
// modifié par Jim le 14/8/2018
echo "<h3>Test de getLesTraces(idUtilisateur) : </h3>";
$lesTraces = $dao->getLesTraces(2);
$nbReponses = sizeof($lesTraces);
echo "<p>Nombre de traces de l'utilisateur 2 : " . $nbReponses . "</p>";
// affichage des traces
foreach ($lesTraces as $uneTrace)
{   echo ($uneTrace->toString());
echo ('<br>');
}


// ferme la connexion à MySQL :
unset($dao);
?>

</body>
</html>