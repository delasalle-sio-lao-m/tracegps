<?php
// Projet TraceGPS - services web
// fichier : api/services/GetUnParcoursEtSesPoints.php
// Dernière mise à jour : 3/7/2019 par Alan Cormier

// connexion du serveur web à la base MySQL
$dao = new DAO();
	
// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$mdp = ( empty($this->request['mdp'])) ? "" : $this->request['mdp'];
$idTrace = ( empty($this->request['idTrace'])) ? "" : $this->request['idTrace'];
$lang = ( empty($this->request['lang'])) ? "" : $this->request['lang'];

// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";

// initialisation du nombre des collections
$laTrace = null;
$lesPointsDeLaTrace = array();

// La méthode HTTP utilisée doit être GET
if ($this->getMethodeRequete() != "GET") {
    $msg = "Erreur : méthode HTTP incorrecte.";
    $code_reponse = 406;
}
else {
    // Les paramètres doivent être présents
    if ( $pseudo == "" || $mdp == "" || $idTrace == "") {
        $msg = "Erreur : données incomplètes.";
        $code_reponse = 400;
    }
    else {
        if ( $dao->getNiveauConnexion($pseudo, $mdp) == 0 ) {
            $msg = "Erreur : authentification incorrecte.";
            $code_reponse = 400;
        }
        else {
            $uneTrace = $dao->getUneTrace($idTrace);
            if ( $uneTrace == null ) {
                $msg = "Erreur : parcours inexistant.";
                $code_reponse = 400;
            }
            else {
                $idAutorisant = $uneTrace->getIdUtilisateur();
                $idAutorise = $dao->getUnUtilisateur($pseudo)->getId();
                $ok = $dao->autoriseAConsulter($idAutorisant, $idAutorise);
                if ($ok == False){
                    $msg =  "Erreur : vous n'êtes pas autorisé par le propriétaire du parcours";
                    $code_reponse = 401;
                }
                else {
            	    $laTrace = $dao->getUneTrace($idTrace);
            	    $lesPointsDeLaTrace = $dao->getLesPointsDeTrace($idTrace);
            	    $msg = "Données de la trace demandée.";
            	    $code_reponse = 200;
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
    $donnees = creerFluxXML($msg, $laTrace, $lesPointsDeLaTrace);
}
else {
    $content_type = "application/json; charset=utf-8";      // indique le format Json pour la réponse
    $donnees = creerFluxJSON($msg, $laTrace, $lesPointsDeLaTrace);
}

// envoi de la réponse HTTP
$this->envoyerReponse($code_reponse, $content_type, $donnees);

// fin du programme (pour ne pas enchainer sur les 2 fonctions qui suivent)
exit;

// ================================================================================================
 
// création du flux XML en sortie
function creerFluxXML($msg, $laTrace, $lesPointsDeLaTrace)
{	
    // crée une instance de DOMdocument (DOM : Document Object Model)
	$doc = new DOMDocument();
	
	// specifie la version et le type d'encodage
	$doc->version = '1.0';
	$doc->encoding = 'UTF-8';
	
	// crée un commentaire et l'encode en UTF-8
	$elt_commentaire = $doc->createComment('Service web GetUnParcoursEtSesPoints - BTS SIO - Lycée De La Salle - Rennes');
	// place ce commentaire à la racine du document XML
	$doc->appendChild($elt_commentaire);
	
	// crée l'élément 'data' à la racine du document XML
	$elt_data = $doc->createElement('data');
	$doc->appendChild($elt_data);
	
	// place l'élément 'reponse' dans l'élément 'data'
	$elt_reponse = $doc->createElement('reponse', $msg);
	$elt_data->appendChild($elt_reponse);
	
	if ($laTrace != null)
	{
	    // place l'élément 'donnees' dans l'élément 'data'
	    $elt_donnees = $doc->createElement('donnees');
	    $elt_data->appendChild($elt_donnees);
	    
	    // place l'élément 'trace' dans l'élément 'donnees'
	    $elt_laTrace = $doc->createElement('trace');
	    $elt_donnees->appendChild($elt_laTrace);
	    
	    // crée les éléments enfants de l'élément 'trace'
	    $elt_id = $doc->createElement('id', $laTrace->getId());
	    $elt_laTrace->appendChild($elt_id);
	    
	    $elt_dateHeureDebut = $doc->createElement('dateHeureDebut', $laTrace->getDateHeureDebut());
	    $elt_laTrace->appendChild($elt_dateHeureDebut);
	    
	    $elt_longitude = $doc->createElement('terminee', $laTrace->getTerminee());
	    $elt_laTrace->appendChild($elt_longitude);
	    
	    $elt_dateHeureFin = $doc->createElement('dateHeureFin', $laTrace->getDateHeureFin());
	    $elt_laTrace->appendChild($elt_dateHeureFin);
	    
	    $elt_idUtilisateur = $doc->createElement('idUtilisateur', $laTrace->getIdUtilisateur());
	    $elt_laTrace->appendChild($elt_idUtilisateur);
	    
	    // traitement des points
	    if (sizeof($lesPointsDeLaTrace) > 0) {
	        
	        // place l'élément 'lesPoints' dans l'élément 'donnees'
	        $elt_lesPointsDeTrace = $doc->createElement('lesPoints');
	        $elt_donnees->appendChild($elt_lesPointsDeTrace);
	        
	        foreach ($lesPointsDeLaTrace as $unPoint)
	        {
	            // crée un élément vide 'point'
	            $elt_point = $doc->createElement('point');
	            
	            // place l'élément 'point' dans l'élément 'lesPointsDeTrace'
	            $elt_lesPointsDeTrace->appendChild($elt_point);
	            
	            // crée les éléments enfants de l'élément 'point'
	            $elt_id         = $doc->createElement('id', $unPoint->getId());
	            $elt_point->appendChild($elt_id);
	            
	            $elt_pseudo     = $doc->createElement('latitude', $unPoint->getLatitude());
	            $elt_point->appendChild($elt_pseudo);
	            
	            $elt_adrMail    = $doc->createElement('longitude', $unPoint->getLongitude());
	            $elt_point->appendChild($elt_adrMail);
	            
	            $elt_numTel     = $doc->createElement('altitude', $unPoint->getAltitude());
	            $elt_point->appendChild($elt_numTel);
	            
	            $elt_niveau     = $doc->createElement('dateHeure', $unPoint->getDateHeure());
	            $elt_point->appendChild($elt_niveau);
	            
	            $elt_dateCreation = $doc->createElement('rythmeCardio', $unPoint->getRythmeCardio());
	            $elt_point->appendChild($elt_dateCreation);
	        }
	    }
	}
	
	// Mise en forme finale
	$doc->formatOutput = true;
	
	// renvoie le contenu XML
	return $doc->saveXML();
}

// ================================================================================================

// création du flux JSON en sortie
function creerFluxJSON($msg, $laTrace, $lesPointsDeLaTrace)
{
    if (sizeof($lesPointsDeLaTrace) == 0) {
        // construction de l'élément "data"
        $elt_data = ["reponse" => $msg];
    }
    else {
        // construction d'un tableau contenant les utilisateurs
        $lesObjetsDuTableau = array();
        
        // construction de l'élément "lesPoints"
        $elt_point = ["lesPoints" => $lesObjetsDuTableau];
        $lesObjetsDuTableau["id"] = $laTrace->getId();
        $lesObjetsDuTableau["dateHeureDebut"] = $laTrace->getDateHeureDebut();
        $lesObjetsDuTableau["terminee"] = $laTrace->getTerminee();
        $lesObjetsDuTableau["dateHeureFin"] = $laTrace->getDateHeureFin();
        $lesObjetsDuTableau["idUtilisateur"] = $laTrace->getIdUtilisateur();
        
        // construction de l'élément "lesPoints"
        $elt_point = ["trace" => $lesObjetsDuTableau];
        
        // vidage du tableau
        $lesObjetsDuTableau = array();
        
        foreach ($lesPointsDeLaTrace as $unPoint)
        {	// crée une ligne dans le tableau
            $unObjetUtilisateur = array();
            $unObjetUtilisateur["id"] = $unPoint->getId();
            $unObjetUtilisateur["latitude"] = $unPoint->getLatitude();
            $unObjetUtilisateur["longitude"] = $unPoint->getLongitude();
            $unObjetUtilisateur["altitude"] = $unPoint->getAltitude();
            $unObjetUtilisateur["dateheure"] = $unPoint->getDateHeure();
            $unObjetUtilisateur["rythmeCardio"] = $unPoint->getRythmeCardio();
            
            $lesObjetsDuTableau[] = $unObjetUtilisateur;
        }
        // construction de l'élément "lesPoints"
        $elt_point += ["lesPoints" => $lesObjetsDuTableau];
        
        // construction de l'élément "data"
        $elt_data = ["reponse" => $msg, "donnees" => $elt_point];
    }
    
    // construction de la racine
    $elt_racine = ["data" => $elt_data];
    
    // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
    return json_encode($elt_racine, JSON_PRETTY_PRINT);
}

// ================================================================================================
?>
