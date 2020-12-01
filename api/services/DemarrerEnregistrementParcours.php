<?php
// Projet TraceGPS - services web
// fichier :  api/services/CreerUnUtilisateur.php
// Dernière mise à jour : 3/7/2019 par Jim

// Rôle : ce service permet à un utilisateur de se créer un compte
// Le service web doit recevoir 4 paramètres :
//     pseudo : le pseudo de l'utilisateur
//     adrMail : son adresse mail
//     numTel : son numéro de téléphone
//     lang : le langage du flux de données retourné ("xml" ou "json") ; "xml" par défaut si le paramètre est absent ou incorrect
// Le service retourne un flux de données XML ou JSON contenant un compte-rendu d'exécution

// Les paramètres doivent être passés par la méthode GET :
//     http://<hébergeur>/tracegps/api/CreerUnUtilisateur?pseudo=turlututu&adrMail=delasalle.sio.eleves@gmail.com&numTel=1122334455&lang=xml

// connexion du serveur web à la base MySQL
$dao = new DAO();

// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$mdpSha1 = ( empty($this->request['mdp'])) ? "" : $this->request['mdp'];
$lang = ( empty($this->request['lang'])) ? "" : $this->request['lang'];


// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";

$uneTrace = null;

// La méthode HTTP utilisée doit être GET
if ($this->getMethodeRequete() != "GET")
{	$msg = "Erreur : méthode HTTP incorrecte.";
    $code_reponse = 406;
}
else 
{
    // Les paramètres doivent être présents
    if (($pseudo == '' || $mdpSha1 == '' ) ) 
    {
    	$msg = "Erreur : données incomplètes.";
    	$code_reponse = 400;
    }
    else 
    {
        if ( $dao->getNiveauConnexion($pseudo, $mdpSha1) == 0 ) 
        {
    		$msg = "Erreur : authentification incorrecte.";
    		$code_reponse = 400;
    	}
    	else 
    	{	$unId = 0;   
            $unIdUtilisateur = $dao->getUnUtilisateur($pseudo)->getId();
		    $uneDateHeureDebut = date('Y-m-d H:i:s', time()); 
		    $uneDateHeureFin = null;
		    $terminee = 0;
		    $uneTrace = new Trace($unId, $uneDateHeureDebut, $uneDateHeureFin, $terminee, $unIdUtilisateur);
		    $ok = $dao->creerUneTrace($uneTrace);
		    $uneIdTrace = $uneTrace->getId();
		    if ( ! $ok ) 
		    {
		        $msg = "Erreur : problème lors de l'enregistrement.";
		        $code_reponse = 400;		        
		    }
		    else 
		    {
		        $msg = "Trace créée.";
		        $code_reponse = 201;
		    }

		}
	}
}
    

// ferme la connexion à MySQL :
unset($dao);

// création du flux en sortie
if ($lang == "xml") {
    $content_type = "application/xml; charset=utf-8";      // indique le format XML pour la réponse
    $donnees = creerFluxXML ($msg, $uneTrace);
}
else {
    $content_type = "application/json; charset=utf-8";      // indique le format Json pour la réponse
    $donnees = creerFluxJSON ($msg, $uneTrace);
}

// envoi de la réponse HTTP
$this->envoyerReponse($code_reponse, $content_type, $donnees);

// fin du programme (pour ne pas enchainer sur les 2 fonctions qui suivent)
exit;

// ================================================================================================

// création du flux XML en sortie
function creerFluxXML($msg, Trace $uneTrace)
{	
    /* Exemple de code XML
        <?xml version="1.0" encoding="UTF-8"?>
        <!--Service web CreerUnUtilisateur - BTS SIO - Lycée De La Salle - Rennes-->
        <data>
          <reponse>Erreur : pseudo trop court (8 car minimum) ou déjà existant .</reponse>
        </data>
     */
    
    // crée une instance de DOMdocument (DOM : Document Object Model)
	$doc = new DOMDocument();	

	// specifie la version et le type d'encodage
	$doc->version = '1.0';
	$doc->encoding = 'UTF-8';
	
	// crée un commentaire et l'encode en UTF-8
	$elt_commentaire = $doc->createComment('Service web DemarrerEnregistrementParcours - BTS SIO - Lycée De La Salle - Rennes');
	// place ce commentaire à la racine du document XML
	$doc->appendChild($elt_commentaire);
		
	// crée l'élément 'data' à la racine du document XML
	$elt_data = $doc->createElement('data');
	$doc->appendChild($elt_data);
	
	// place l'élément 'reponse' juste après l'élément 'data'
	$elt_reponse = $doc->createElement('reponse', $msg);
	$elt_data->appendChild($elt_reponse);
	
	if( $uneTrace != null){
	    $elt_donnees = $doc->createElement('donnees');
	    $elt_data->appendChild($elt_donnees);
	    
	    $elt_trace = $doc->createElement('trace');
	    $elt_data->appendChild($elt_trace);
	    
	    $elt_id = $doc->createElement('id', $uneTrace->getId());
	    $elt_trace->appendChild($elt_id);
	    
	    $elt_dateHeureDebut = $doc->createElement('uneDateHeureDebut', $uneTrace->getDateHeureDebut());
	    $elt_trace->appendChild($elt_dateHeureDebut);
	    
	    $elt_terminee = $doc->createElement('terminee', $uneTrace->getTerminee());
	    $elt_trace->appendChild($elt_terminee);
	    
	    $elt_idUtilisateur = $doc->createElement('idUtilisateur', $uneTrace->getIdUtilisateur());
	    $elt_trace->appendChild($elt_idUtilisateur);
	}
	


    
    
	
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
                "reponse": "Erreur : pseudo trop court (8 car minimum) ou d\u00e9j\u00e0 existant."
            }
        }
     */
    if($uneTrace != null){
        
        $elt_idTrace = ["id" => $uneTrace->getId()];
        $elt_data = ["reponse" => $msg, "donnees" => $elt_idTrace];
        
        
        
        $elt_dateHeureDebut = ["uneDateHeureDebut" => $uneTrace->getDateHeureDebut()];
        $elt_data->appendChild($elt_dateHeureDebut);
        
        $elt_terminee = ["terminee"=> $uneTrace->getTerminee()];
        $elt_data->appendChild($elt_terminee);
        
        $elt_idUtilisateur = ["idUtilisateur"=> $uneTrace->getIdUtilisateur()];
        $elt_data->appendChild($elt_idUtilisateur);
        
        
    }
    else{
        $elt_data = ["reponse" => $msg, "donnees" => $elt_idTrace];
    }
    // construction de l'élément "data"
    // construction de la racine
    $elt_racine = ["data" => $elt_data];
    
    // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
    return json_encode($elt_racine, JSON_PRETTY_PRINT);
}

// ================================================================================================
?>