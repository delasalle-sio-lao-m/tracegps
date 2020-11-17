<?php
// Projet TraceGPS
// fichier : modele/DAO.test1.php
// Rôle : test de la classe DAO.class.php
// Dernière mise à jour : 13/10/2020 par Alan Cormier

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


// test de la méthode getLesUtilisateursAutorises -------------------------------------------------
// modifié par Jim le 13/8/2018
echo "<h3>Test de getLesUtilisateursAutorises(idUtilisateur) : </h3>";
$lesUtilisateurs = $dao->getLesUtilisateursAutorises(2);
$nbReponses = sizeof($lesUtilisateurs);
echo "<p>Nombre d'utilisateurs autorisés par l'utilisateur 2 : " . $nbReponses . "</p>";
// affichage des utilisateurs
foreach ($lesUtilisateurs as $unUtilisateur)
{	echo ($unUtilisateur->toString());
echo ('<br>');
}




/*
// test de la méthode creerUnPointDeTrace ---------------------------------------------------------
// modifié par Jim le 13/8/2018
echo "<h3>Test de creerUnPointDeTrace : </h3>";
// on affiche d'abord le nombre de points (5) de la trace 1
$lesPoints = $dao->getLesPointsDeTrace(1);
$nbPoints = sizeof($lesPoints);
echo "<p>Nombre de points de la trace 1 : " . $nbPoints . "</p>";
// on crée un sixième point et on l'ajoute à la trace 1
$unIdTrace = 1;
$unID = 1;
$uneLatitude = 48.20;
$uneLongitude = -1.55;
$uneAltitude = 50;
$uneDateHeure = date('Y-m-d H:i:s', time());
$unRythmeCardio = 80;
$unTempsCumule = 0;
$uneDistanceCumulee = 0;
$uneVitesse = 15;
$unPoint = new PointDeTrace($unIdTrace, $unID, $uneLatitude, $uneLongitude, $uneAltitude, $uneDateHeure, $unRythmeCardio, $unTempsCumule, $uneDistanceCumulee, $uneVitesse);
$ok = $dao->creerUnPointDeTrace($unPoint);
if ($ok)
{
// on affiche à nouveau le nombre de points (6) de la trace 1
$lesPoints = $dao->getLesPointsDeTrace(1);
$nbPoints = sizeof($lesPoints);
echo "<p>Nombre de points de la trace 1 : " . $nbPoints . "</p>";
echo ('<br>');
}
else
{
    echo "<p>Point déjà enregistré !</p>";
}
*/



// test de la méthode getLesTracesAutorisees($idUtilisateur) --------------------------------------
// modifié par Jim le 14/8/2018
echo "<h3>Test de getLesTracesAutorisees(idUtilisateur) : </h3>";
$lesTraces = $dao->getLesTracesAutorisees(2);
$nbReponses = sizeof($lesTraces);
echo "<p>Nombre de traces autorisées à l'utilisateur 2 : " . $nbReponses . "</p>";
// affichage des traces
foreach ($lesTraces as $uneTrace)
{   echo ($uneTrace->toString());
echo ('<br>');
}
$lesTraces = $dao->getLesTracesAutorisees(3);
$nbReponses = sizeof($lesTraces);
echo "<p>Nombre de traces autorisées à l'utilisateur 3 : " . $nbReponses . "</p>";
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