<?php
// Projet TraceGPS
// fichier : modele/DAO.test1.php
// Rôle : test de la classe DAO.class.php
// Dernière mise à jour : 13/10/2020 par Theo Jouan

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


// test de la méthode existeAdrMailUtilisateur -----------------------------------------------------
echo "<h3>Test de existeAdrMailUtilisateur : </h3>";
if ($dao->existeAdrMailUtilisateur("delasalle.sio.lao.m@gmail.com")) $existe = "oui"; else $existe = "non";
echo "<p>Existence de l'utilisateur 'delasalle.sio.lao.m@gmail.com' : <b>" . $existe . "</b><br>";
if ($dao->existeAdrMailUtilisateur("delasalle.sio.jouan.t@gmail.com")) $existe = "oui"; else $existe = "non";
echo "Existence de l'utilisateur 'delasalle.sio.jouan.t@gmail.com' : <b>" . $existe . "</b></br>";
if ($dao->existeAdrMailUtilisateur("delasalle.sio.jouant.t@gmail.com")) $existe = "oui"; else $existe = "non";
echo "Existence de l'utilisateur 'delasalle.sio.jouant.t@gmail.com' : <b>" . $existe . "</b></p>";











// ferme la connexion à MySQL :
unset($dao);
?>

</body>
</html>