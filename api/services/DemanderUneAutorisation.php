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
    {	$msg = "Erreur : données incomplètes.";
    $code_reponse = 400;
    }
    
    else
    {	// test de l'authentification de l'utilisateur
        // la méthode getNiveauConnexion de la classe DAO retourne les valeurs 0 (non identifié) ou 1 (utilisateur) ou 2 (administrateur)
         
        if ( $dao->getNiveauConnexion($pseudo, $mdpSha1) == 0 ) {
            $msg = "Erreur : authentification incorrecte.";
            $code_reponse = 401;
        }
        
        else
        {	
            if (!$dao->existePseudoUtilisateur($pseudoDestinataire))
            {	$msg = "Erreur : pseudo utilisateur inexistant.";
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
                    $msg = "Erreur : l'envoi du courriel au demandeur a rencontré un problème.";
                    $code_reponse = 500;
                }
                else {
                    $msg = "Autorisation enregistrée. ". $pseudoDestinataire . " va recevoir un courriel de confirmation.";
                    $code_reponse = 200;
                }
                    
             }
        }
    }
}
unset($dao);   // ferme la connexion à MySQL

// création du flux en sortie
if ($lang == "xml") {
    $content_type = "application/xml; charset=utf-8";      // indique le format XML pour la réponse
    $donnees = creerFluxXML($msg);
}
else {
    $content_type = "application/json; charset=utf-8";      // indique le format Json pour la réponse
    $donnees = creerFluxJSON($msg);
}

// envoi de la réponse HTTP
$this->envoyerReponse($code_reponse, $content_type, $donnees);

// fin du programme (pour ne pas enchainer sur les 2 fonctions qui suivent)
exit;

// ================================================================================================

// création du flux XML en sortie
function creerFluxXML($msg)
{
    /*
     * Exemple de code XML
     <?xml version="1.0" encoding="UTF-8"?>
     <!--Service web DemanderUneAutorisation - BTS SIO - Lycée De La Salle - Rennes-->
     <data>
     <reponse>............. (message retourné par le service web) ...............</reponse>
     </data>
     */
    
    
    // crée une instance de DOMdocument (DOM : Document Object Model)
    $doc = new DOMDocument();
    
    // specifie la version et le type d'encodage
    $doc->version = '1.0';
    $doc->encoding = 'UTF-8';
    
    // crée un commentaire et l'encode en UTF-8
    $elt_commentaire = $doc->createComment('Service web DemanderUneAutorisation - BTS SIO - Lycée De La Salle - Rennes');
    // place ce commentaire à la racine du document XML
    $doc->appendChild($elt_commentaire);
    
    // crée l'élément 'data' à la racine du document XML
    $elt_data = $doc->createElement('data');
    $doc->appendChild($elt_data);
    
    // place l'élément 'reponse' dans l'élément 'data'
    $elt_reponse = $doc->createElement('reponse', $msg);
    $elt_data->appendChild($elt_reponse);
    
    // Mise en forme finale
    $doc->formatOutput = true;
    
    // renvoie le contenu XML
    return $doc->saveXML();
}

// ================================================================================================

// création du flux JSON en sortie
function creerFluxJSON($msg)
{
    /* Exemple de code JSON
     {
     "data": {
     "reponse": "Erreur : authentification incorrecte."
     }
     }
     */
    
    // construction de l'élément "data"
    $elt_data = ["reponse" => $msg];
    
    // construction de la racine
    $elt_racine = ["data" => $elt_data];
    
    // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
    return json_encode($elt_racine, JSON_PRETTY_PRINT);
}

// ================================================================================================
?>