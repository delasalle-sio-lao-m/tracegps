<?php
// Projet TraceGPS
// fichier : modele/Trace.class.php
// Rôle : la classe Trace représente une trace ou un parcours
// Dernière mise à jour : 9/9/2019 par Alan Cormier

include_once ('PointDeTrace.class.php');

class Trace
{
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------- Attributs privés de la classe -------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    private $id;				// identifiant de la trace
    private $dateHeureDebut;		// date et heure de début
    private $dateHeureFin;		// date et heure de fin
    private $terminee;			// true si la trace est terminée, false sinon
    private $idUtilisateur;		// identifiant de l'utilisateur ayant créé la trace
    private $lesPointsDeTrace;		// la collection (array) des objets PointDeTrace formant la trace
    
    // ------------------------------------------------------------------------------------------------------
    // ----------------------------------------- Constructeur -----------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function __construct($unId, $uneDateHeureDebut, $uneDateHeureFin, $terminee, $unIdUtilisateur) {
        $this->id = $unId;
        $this->dateHeureDebut = $uneDateHeureDebut;
        $this->dateHeureFin = $uneDateHeureFin;
        $this->terminee = $terminee;
        $this->idUtilisateur = $unIdUtilisateur;
        $this->lesPointsDeTrace = array();
    }
    
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------------- Getters et Setters ------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function getId() {return $this->id;}
    public function setId($unId) {$this->id = $unId;}
    
    public function getDateHeureDebut() {return $this->dateHeureDebut;}
    public function setDateHeureDebut($uneDateHeureDebut) {$this->dateHeureDebut = $uneDateHeureDebut;}
    
    public function getDateHeureFin() {return $this->dateHeureFin;}
    public function setDateHeureFin($uneDateHeureFin) {$this->dateHeureFin= $uneDateHeureFin;}
    
    public function getTerminee() {return $this->terminee;}
    public function setTerminee($terminee) {$this->terminee = $terminee;}
    
    public function getIdUtilisateur() {return $this->idUtilisateur;}
    public function setIdUtilisateur($unIdUtilisateur) {$this->idUtilisateur = $unIdUtilisateur;}
    
    public function getLesPointsDeTrace() {return $this->lesPointsDeTrace;}
    public function setLesPointsDeTrace($lesPointsDeTrace) {$this->lesPointsDeTrace = $lesPointsDeTrace;}
    
    // Fournit une chaine contenant toutes les données de l'objet
    public function toString() {
        $msg = "Id : " . $this->getId() . "<br>";
        $msg .= "Utilisateur : " . $this->getIdUtilisateur() . "<br>";
        if ($this->getDateHeureDebut() != null) {
            $msg .= "Heure de début : " . $this->getDateHeureDebut() . "<br>";
        }
        if ($this->getTerminee()) {
            $msg .= "Terminée : Oui  <br>";
        }
        else {
            $msg .= "Terminée : Non  <br>";
        }
        $msg .= "Nombre de points : " . $this->getNombrePoints() . "<br>";
        if ($this->getNombrePoints() > 0) {
            if ($this->getDateHeureFin() != null) {
                $msg .= "Heure de fin : " . $this->getDateHeureFin() . "<br>";
            }
            $msg .= "Durée en secondes : " . $this->getDureeEnSecondes() . "<br>";
            $msg .= "Durée totale : " . $this->getDureeTotale() . "<br>";
            $msg .= "Distance totale en Km : " . $this->getDistanceTotale() . "<br>";
            $msg .= "Dénivelé en m : " . $this->getDenivele() . "<br>";
            $msg .= "Dénivelé positif en m : " . $this->getDenivelePositif() . "<br>";
            $msg .= "Dénivelé négatif en m : " . $this->getDeniveleNegatif() . "<br>";
            $msg .= "Vitesse moyenne en Km/h : " . $this->getVitesseMoyenne() . "<br>";
            $msg .= "Centre du parcours : " . "<br>";
            $msg .= "   - Latitude : " . $this->getCentre()->getLatitude() . "<br>";
            $msg .= "   - Longitude : "  . $this->getCentre()->getLongitude() . "<br>";
            $msg .= "   - Altitude : " . $this->getCentre()->getAltitude() . "<br>";
        }
        return $msg;
    }
    
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------------- Méthodes d'instances ----------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    // Fournit une chaine contenant toutes les données de l'objet
    public function getNombrePoints() {
        $nbrePoints = sizeof($this->lesPointsDeTrace);
        return $nbrePoints;
    }
    
    public function getCentre(){
        $latMin = $this->lesPointsDeTrace[0]->getLatitude();
        $latMax = $this->lesPointsDeTrace[sizeof($this->lesPointsDeTrace) - 1]->getLatitude();
        $longMin = $this->lesPointsDeTrace[0]->getLongitude();
        $longMax = $this->lesPointsDeTrace[sizeof($this->lesPointsDeTrace) - 1]->getLongitude();
        
        
        for ($i = 0; $i < sizeof($this->lesPointsDeTrace) ; $i++) {
            $lePoint = $this->lesPointsDeTrace[$i];
            
        if($lePoint->getLatitude() > $latMax)
            $latMax = $lePoint->getLatitude();
                
        if($lePoint->getLatitude() < $latMin)
            $latMin = $lePoint->getLatitude();
                    
        if($lePoint->getLongitude() > $longMax)
            $longMax = $lePoint->getLongitude();
                        
        if($lePoint->getLongitude() < $longMin)
            $longMin = $lePoint->getLongitude();
                            
        }
        
        $latCentre = ($latMax + $latMin) / 2;
        $longCentre = ($longMax + $longMin) / 2;
        
        return new Point($latCentre, $longCentre, 0);
    }
    
    public function getDenivele(){
        $altMin = $this->lesPointsDeTrace[0]->getAltitude();
        $altMax = $this->lesPointsDeTrace[0]->getAltitude();
        
        
        for ($i = 0; $i < sizeof($this->lesPointsDeTrace) ; $i++) {
            $lePoint = $this->lesPointsDeTrace[$i];
            
        if($lePoint->getAltitude() > $altMax)
            $altMax = $lePoint->getAltitude();
                
        if($lePoint->getAltitude() < $altMin)
            $altMin = $lePoint->getAltitude();
        }
        
        return $altMax - $altMin;
    }
    
    public function getDureeEnSecondes(){
        if (sizeof($this->lesPointsDeTrace) == 0) return 0;
        $tempsCumule = $this->lesPointsDeTrace[sizeof($this->lesPointsDeTrace) - 1]->getTempsCumule();
        return $tempsCumule;
    }
    
    public function getDureeTotale(){
        
        $duree = Trace::getDureeEnSecondes();
        $heures=intval($duree / 3600);
        $minutes=intval(($duree % 3600) / 60);
        $secondes=intval((($duree % 3600) % 60));
        
        
        return sprintf("%02d",$heures) . ":" . sprintf("%02d",$minutes) . ":" . sprintf("%02d",$secondes);
    }
    
    public function getDistanceTotale()
    {
        if(sizeof($this->lesPointsDeTrace) == 0) return 0;
        return $this->lesPointsDeTrace[sizeof($this->lesPointsDeTrace) - 1]->getDistanceCumulee();
        
    }
    
    public function getDenivelePositif(){
        
        $denivele = 0;
        
        for ($i = 0; $i < sizeof($this->lesPointsDeTrace) - 1 ; $i++) {
            $lePoint = $this->lesPointsDeTrace[$i];
            $autrePoint = $this->lesPointsDeTrace[$i + 1];
            
            if($autrePoint->getAltitude() > $lePoint->getAltitude())
                $denivele += $autrePoint->getAltitude() - $lePoint->getAltitude();
        }
        
        return $denivele;
    }
    
    public function getDeniveleNegatif(){
        
        $denivele = 0;
        
        for ($i = 0; $i < sizeof($this->lesPointsDeTrace) - 1 ; $i++) {
            $lePoint = $this->lesPointsDeTrace[$i];
            $autrePoint = $this->lesPointsDeTrace[$i + 1];
            
            if($autrePoint->getAltitude() < $lePoint->getAltitude())
                $denivele+= $autrePoint->getAltitude();
        }
        
        return $denivele;
    }
    
    public function getVitesseMoyenne(){
        $distance = Trace::getDistanceTotale();
        $duree = Trace:: getDureeEnSecondes();
        if($duree == 0)
            return 0;
            
            $vitesse = (($distance * 1000) / $duree) * 3.6;
            return $vitesse;
    }
    
    public function ajouterPoint(PointDeTrace $unPoint){
        if(sizeof($this->lesPointsDeTrace) == 0){
            $unPoint->setTempsCumule(0);
            $unPoint->setDistanceCumulee(0);
            $unPoint->setVitesse(0);
        }
        else {
            
            $dernierPoint = $this->lesPointsDeTrace[sizeof($this->lesPointsDeTrace) - 1];
            
            $duree = strtotime($unPoint->getDateHeure()) - strtotime($dernierPoint->getDateHeure());
            $unPoint->setTempsCumule($dernierPoint->getTempsCumule() + $duree);
            
            $distance = Point::getDistance($dernierPoint, $unPoint);
            $unPoint->setDistanceCumulee($dernierPoint->getDistanceCumulee() + $distance);
            
            if($duree > 0)
            {
                $vitesse = $distance / $duree * 3600;
            }
            else {
                $vitesse = 0;
            }
            
            $unPoint->setVitesse($vitesse);
        }
        $this->lesPointsDeTrace[] = $unPoint;
    }
    
    public function viderListePoints()
    {
        $this->lesPointsDeTrace = array();
    }
    
} // fin de la classe Trace

// ATTENTION : on ne met pas de balise de fin de script pour ne pas prendre le risque
// d'enregistrer d'espaces après la balise de fin de script !!!!!!!!!!!!