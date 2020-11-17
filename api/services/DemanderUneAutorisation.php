<?php
// Projet TraceGPS - services web
// fichier :  api/services/DemandeUneAutorisation.php
// Dernière mise à jour : 17/11/2020 par Monorom

// Rôle : ce service web permet à un utilisateur de demander une autorisation à un autre utilisateur.

// Le service web doit être appelé avec 4 paramètres obligatoires dont les noms sont volontairement non significatifs :
//    a : le mot de passe (hashé) de l'utilisateur destinataire de la demande ($mdpSha1)
//    b : le pseudo de l'utilisateur destinataire de la demande ($pseudoAutorisant)
//    c : le pseudo de l'utilisateur source de la demande ($pseudoAutorise)
//    d : la decision 1=oui, 0=non ($decision)

// Les paramètres doivent être passés par la méthode GET :
//     http://<hébergeur>/tracegps/api/ValiderDemandeAutorisation?a=13e3668bbee30b004380052b086457b014504b3e&b=oxygen&c=europa&d=1

// ces variables globales sont définies dans le fichier modele/parametres.php
global $ADR_MAIL_EMETTEUR, $ADR_SERVICE_WEB;

// connexion du serveur web à la base MySQL
$dao = new DAO();


// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$mdpSha1 = ( empty($this->request['mdp'])) ? "" : $this->request['mdp'];
$pseudoDestinataire = ( empty($this->request['pseudoDestinataire'])) ? "" : $this->request['pseudoDestinataire'];
$texteMessage = ( empty($this->request['texteMessage'])) ? "" : $this->request['texteMessage'];
$nomPrenom = ( empty($this->request['nomPrenom'])) ? "" : $this->request['nomPrenom'];
$lang = ( empty($this->request['lang'])) ? "" : $this->request['lang'];

// La méthode HTTP utilisée doit être GET
if ($this->getMethodeRequete() != "GET")
{	$msg = "Erreur : méthode HTTP incorrecte.";
$code_reponse = 406;
}
else {
    // Les paramètres doivent être présents et corrects
    if ( $pseudo == "" || $mdpSha1 == "" || $pseudoDestinataire == "" || $texteMessage == "" || $nomPrenom == "" || ( $lang != "xml" && $lang != "json" ) )
    {	$message = "Erreur : données incomplètes.";
    $code_reponse = 400;
    }
    
    else
    {	// test de l'authentification de l'utilisateur
        // la méthode getNiveauConnexion de la classe DAO retourne les valeurs 0 (non identifié) ou 1 (utilisateur) ou 2 (administrateur)
        $niveauConnexion = $dao->getNiveauConnexion($pseudo, $mdpSha1);        
        
        if ( $niveauConnexion == 0 )
        {  $message = "Erreur : authentification incorrecte.";
        $code_reponse = 401;
        }
        
        else
        {	
            if (!$dao->existePseudoUtilisateur($pseudoDestinataire))
            {	$message = "Erreur : pseudo utilisateur inexistant.";
            $code_reponse = 400;
            }
            else
            {
                $utilisateurDemandeur = $dao->getUnUtilisateur($pseudo);
                $adrMailDemandeur = $utilisateurDemandeur->getAdrMail();
                 
                // envoi d'un mail d'acceptation à l'intéressé
                $sujetMail = "Votre demande d'autorisation à un utilisateur du système TraceGPS";
                $contenuMail = "Cher ou chère " . $pseudoDestinataire . "\n\n";
                $contenuMail .= "Un utilsateur du système TraceGPS vous demande l'autorisation de suivre votre parcours.\n\n";
                $contenuMail .= "Voici les données le concernant :\n\n";
                $contenuMail .= "Son pseudo : " . $pseudo. "\n";
                $contenuMail .= "Son adresse mail : " . $utilisateurDemandeur->getAdrMail(). "\n";
                $contenuMail .= "Son numéro de téléphone : " . $utilisateurDemandeur->getNumTel(). "\n";
                $contenuMail .= "Son nom et prénom : " . $nomPrenom. "\n";
                $contenuMail .= "Son message : " . $texteMessage. "\n\n";
                $utilisateurDestinataire = $dao->getUnUtilisateur($pseudoDestinataire);
                $mdpDestinataire = $utilisateurDestinataire->getMdpSha1();
              
                $contenuMail .= "Pour accepter la demande, cliquez sur ce lien : \n";
                $contenuMail .= "http://localhost/ws-php-lao/tracegps/api/ValiderDemandeAutorisation?a=". $mdpDestinataire."&b=". $pseudoDestinataire ."&c=".$pseudo. "&d=1"."\n\n";
                $contenuMail .= "Pour refuser la demande, cliquez sur ce lien : \n";
                $contenuMail .= "http://localhost/ws-php-lao/tracegps/api/ValiderDemandeAutorisation?a=". $mdpDestinataire."&b=". $pseudoDestinataire ."&c=".$pseudo. "&d=0"."\n\n";
                $ok = Outils::envoyerMail($adrMailDemandeur, $sujetMail, $contenuMail, $ADR_MAIL_EMETTEUR);
                if ( ! $ok ) {
                    $message = "Erreur : l'envoi du courriel au demandeur a rencontré un problème.";
                    $code_reponse = 500;
                }
                else {
                    $message = "Autorisation enregistrée.<br>". $pseudoDestinataire . " va recevoir un courriel de confirmation.";
                    $code_reponse = 200;
                }
                    
             }
        }
    }
}
unset($dao);   // ferme la connexion à MySQL
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Validation TraceGPS</title>
	<style type="text/css">body {font-family: Arial, Helvetica, sans-serif; font-size: small;}</style>
</head>
<body>
	<p><?php echo $message; ?></p>
	<p><a href="Javascript:window.close();">Fermer</a></p>
</body>
</html>