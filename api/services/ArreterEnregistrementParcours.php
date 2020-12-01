<?php
// Projet TraceGPS - services web
// fichier : api/services/ArreterEnregistrementParcours.php
// Dernière mise à jour : 3/7/2019 par Jim

// Rôle : ce service permet d'arrêter l'enregistement d'un parcours qui le fera passer de 0 à 1
// Le service web doit recevoir 4 paramètres :
//     pseudo : le pseudo de l'utilisateur
//     mdp : le mot de passe de l'utilisateur hashé en sha1
//     lang : le langage du flux de données retourné ("xml" ou "json") ; "xml" par défaut si le paramètre est absent ou incorrect
//     idTrace : l'id du parcours que l'on veut arrêter
// Le service retourne un flux de données XML ou JSON contenant un compte-rendu d'exécution

// connexion du serveur web à la base MySQL
$dao = new DAO();
define('TIMEZONE', 'Europe/Paris'); 
date_default_timezone_set(TIMEZONE);


// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$mdpSha1 = ( empty($this->request['mdp'])) ? "" : $this->request['mdp'];
$lang = ( empty($this->request['lang'])) ? "" : $this->request['lang'];
$idTrace = ( empty($this->request['idTrace'])) ? "" : $this->request['idTrace'];

// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";

$uneTrace = null;
// La méthode HTTP utilisée doit être GETv
if ($this->getMethodeRequete() != "GET")
{
    $msg = "Erreur : méthode HTTP incorrecte.";
    $code_reponse = 406;
}
else {
    // Les paramètres doivent être présents
    if ( $pseudo == "" || $mdpSha1 == ""  || $lang == "" || $idTrace =="") {
        $msg = "Erreur : données incomplètes.";
        $code_reponse = 400;
    }
    else
    {
        if ($dao->getNiveauConnexion($pseudo, $mdpSha1) <= 0) {
            $msg = "Erreur : authentification incorrecte.";
            $code_reponse = 401;
        }
        else
        {
            if($dao->getUneTrace($idTrace) == null) {
                $msg = "Erreur : parcours inexistant.";
                $code_reponse = 401;
            }
            else
            {   $idUtilisateur = $dao->getUnUtilisateur($pseudo)->getId();
            if( in_array($dao->getUneTrace($idTrace), $dao->getLesTraces($idUtilisateur)) != true  ) {
                $msg = "Vous n'êtes pas le propriétaire de ce parcours";
                $code_reponse = 401;
            }
            else
            {
                $uneTrace = $dao->getUneTrace($idTrace);
                if($uneTrace->getTerminee() == 1) {
                    $msg = "Cette trace est déjà terminée.";
                    $code_reponse = 401;
                }
                else {
                    $dao->terminerUneTrace($idTrace);
                    $msg = "Enregistrement terminé.";
                    $code_reponse = 200;
                }
            }
            }
        }
    }
}
// ferme la connexion à MySQL :
unset($dao);
// création du flux en sortie
if ($lang == "xml") {
    $content_type = "application/xml; charset=utf-8";      // indique le format XML pour la réponse
    $donnees = creerFluxXML($msg, $uneTrace);
}
else {
    $content_type = "application/json; charset=utf-8";      // indique le format Json pour la réponse
    $donnees = creerFluxJSON($msg, $uneTrace);
}

// envoi de la réponse HTTP
$this->envoyerReponse($code_reponse, $content_type, $donnees);

// fin du programme (pour ne pas enchainer sur les 2 fonctions qui suivent)
exit;

// ================================================================================================

// création du flux XML en sortie
function creerFluxXML($msg, $uneTrace)
{
    /* Exemple de code XML
     <?xml version="1.0" encoding="UTF-8"?>
     <!--Service web ArreterEnregistrementParcours - BTS SIO - Lycée De La Salle - Rennes-->
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
    $elt_commentaire = $doc->createComment('Service web GetTousLesUtilisateurs - BTS SIO - Lycée De La Salle - Rennes');
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
function creerFluxJSON($msg, $uneTrace)
{
    /* Exemple de code JSON
     {
     "data": {
     "reponse": "............. (message retourné par le service web) ..............."
     }
     }
     */
    if ($uneTrace == null) {
        // construction de l'élément "data"
        $elt_data = ["reponse" => $msg];
    }
    // construction de la racine
    $elt_racine = ["data" => $elt_data];
    
    // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
    return json_encode($elt_racine, JSON_PRETTY_PRINT);
}

// ================================================================================================
