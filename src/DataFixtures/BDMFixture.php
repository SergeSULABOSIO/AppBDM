<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Assureur;
use App\Entity\Automobile;
use App\Entity\Client;
use App\Entity\Contact;
use App\Entity\Entreprise;
use App\Entity\Monnaie;
use App\Entity\Partenaire;
use App\Entity\Produit;
use App\Entity\Taxe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class BDMFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {


        $faker = Factory::create();

        $tabNomsPartenaires = array("AFINBRO", "BOLLORE LUSHI", "MARSH", "O'NEILS");
        $tabMarquesAutomobiles = array("TOYOTA", "NISSAN", "MAZDA", "SUZUKI", "MERCEDES");
        $tabNomsTaxes = array("TVA", "ARCA");
        $tabCodesMonnaies = array("USD", "CDF");
        $tabNomsAssureurs = array("SFA CONGO SA", "ACTIVA", "SUNU", "MAYFAIRE", "RAWSUR", "SONAS");
        $tabNomsProduits = array(
            "INCENDIE ET RISQUES DIVERS / ASSET",
            "RC AUTOMOBILE / MOTOR TPL",
            "TOUS RISQUES AUTOMOBILE / MOTOR COMP",
            "RC GENERALE OU D'EXPLOITATION / GL",
            "RC EMPLOYEUR / EMPL",
            "ACCIDENT DU TRAVAIL / GPA",
            "DEGATS MATERIELS ET PERTES D'EXPLOITATION / PDBI",
            "GLOBALE DES BANQUES / BBB",
            "TRANSPORT DES FRONDS / CIT",
            "TRANSPORT DES FACULTES / GIT",
            "RISQUES POLITIQUES ET TERRORISME / PVT"
        );

        //ENTREPRISE
        $entreprise = new Entreprise();
        $entreprise->setNom("AIB RDC Sarl");
        $entreprise->setAdresse("Avenue de la Gombe, Kinshasa / RDC");
        $entreprise->setIdnat("IDNAT00045");
        $entreprise->setNumimpot("NUMIMPO00124545");
        $entreprise->setRccm("RCCM045CDKIN");
        $entreprise->setSecteur(2);
        $entreprise->setTelephone("+243828727706");
        $manager->persist($entreprise);

        //MONNAIES
        $monnaieUSD = null;
        foreach ($tabCodesMonnaies as $codeMonnaie) {
            //Pour chaque element du tableau
            $monnaie = new Monnaie();
            if ($codeMonnaie == "CDF") {
                $monnaie->setNom("Franc Congolais");
                $monnaie->setTauxusd(1);
                $monnaie->setIslocale(true);
            } else {
                $monnaie->setNom("Dollars Américains");
                $monnaie->setTauxusd(2050);
                $monnaie->setIslocale(false);
                $monnaieUSD = $monnaie;
            }
            $monnaie->setCode($codeMonnaie);
            $monnaie->setEntreprise($entreprise);
            $manager->persist($monnaie);
        }

        //TAXES
        foreach ($tabNomsTaxes as $nomTaxes) {
            //Pour chaque element du tableau
            $taxe = new Taxe();
            $taxe->setNom($nomTaxes);
            if ($nomTaxes == "TVA") {
                $taxe->setDescription("Taxe sur la Valeur Ajoutée");
                $taxe->setTaux(16);
                $taxe->setPayableparcourtier(false);
                $taxe->setOrganisation("DGI - Direction Générale des Impôts");
            } else {
                $taxe->setDescription("Frais de surveillance");
                $taxe->setTaux(2);
                $taxe->setPayableparcourtier(true);
                $taxe->setOrganisation("ARCA - Autorité de Régulation des Assurances");
            }
            $taxe->setEntreprise($entreprise);
            $manager->persist($taxe);
        }


        //PARTENAIRES
        foreach ($tabNomsPartenaires as $nomPartenaire) {
            $partenaire = new Partenaire();
            $partenaire->setNom($nomPartenaire);
            $partenaire->setAdresse($faker->address());
            $partenaire->setEmail($faker->email());
            $partenaire->setSiteweb($faker->url());
            $partenaire->setRccm("RCCM" . $faker->randomNumber(5, true));
            $partenaire->setIdnat("IDNAT" . $faker->randomNumber(5, true));
            $partenaire->setNumimpot("IMP" . $faker->randomNumber(5, true));
            $partenaire->setPart(50);
            $partenaire->setEntreprise($entreprise);
            $manager->persist($partenaire);
        }

        //ASSUREURS
        foreach ($tabNomsAssureurs as $nomAssureur) {
            $assureur = new Assureur();
            $assureur->setNom($nomAssureur);
            $assureur->setAdresse($faker->address());
            $assureur->setTelephone($faker->phoneNumber());
            $assureur->setEmail($faker->email());
            $assureur->setSiteweb($faker->url());
            $assureur->setRccm("RCCM" . $faker->randomNumber(5, true));
            $assureur->setIdnat("IDNAT" . $faker->randomNumber(5, true));
            $assureur->setLicence("ARCA" . $faker->randomNumber(3, true));
            $assureur->setNumimpot("IMP" . $faker->randomNumber(5, true));
            $assureur->setIsreassureur(false);
            $assureur->setEntreprise($entreprise);
            $manager->persist($assureur);
        }

        //Autres assureurs
        for ($i=0; $i < 50 ; $i++) { 
            $assureur = new Assureur();
            $assureur->setNom($faker->company()." Insurance LTD");
            $assureur->setAdresse($faker->address());
            $assureur->setTelephone($faker->phoneNumber());
            $assureur->setEmail($faker->email());
            $assureur->setSiteweb($faker->url());
            $assureur->setRccm("RCCM" . $faker->randomNumber(5, true));
            $assureur->setIdnat("IDNAT" . $faker->randomNumber(5, true));
            $assureur->setLicence("ARCA" . $faker->randomNumber(3, true));
            $assureur->setNumimpot("IMP" . $faker->randomNumber(5, true));
            $assureur->setIsreassureur(true);
            $assureur->setEntreprise($entreprise);
            $manager->persist($assureur);
        }

        //PRODUIT
        $compteur = 0;
        foreach ($tabNomsProduits as $nomProduit) {
            $produit = new Produit();
            $produit->setNom($nomProduit);
            $produit->setDescription($faker->sentence(5));
            if ($compteur % 2) {
                $produit->setIsobligatoire(true);
                $produit->setTauxarca(10);
            } else {
                $produit->setIsobligatoire(false);
                $produit->setTauxarca(15);
            }
            $produit->setIsabonnement(false);
            $produit->setCategorie(0);
            $produit->setEntreprise($entreprise);
            $manager->persist($produit);
            $compteur++;
        }

        //CLIENTS
        $compteur = 0;
        for ($i = 0; $i < 100; $i++) {
            $client = new Client();
            $client->setAdresse($faker->address());
            $client->setTelephone($faker->phoneNumber());
            $client->setEmail($faker->email());
            $client->setSiteweb($faker->url());
            if ($compteur < 30) {
                $client->setNom($faker->name());
                $client->setIspersonnemorale(false);
                $client->setRccm("");
                $client->setIdnat("");
                $client->setNumipot("");
                $client->setSecteur(0);
            } else {
                $client->setNom($faker->company());
                $client->setIspersonnemorale(true);
                $client->setRccm("RCCM" . $faker->randomNumber(5, true));
                $client->setIdnat("IDNAT" . $faker->randomNumber(5, true));
                $client->setNumipot("IMP" . $faker->randomNumber(5, true));
                $client->setSecteur(2);
            }
            $client->setEntreprise($entreprise);
            $manager->persist($client);
            $compteur++;

            //Chaque client a des contacts
            for ($j = 0; $j < 3; $j++) {
                $contact = new Contact();
                $contact->setNom($faker->name());
                $contact->setPoste($faker->jobTitle());
                $contact->setTelephone($faker->phoneNumber());
                $contact->setEmail($faker->email());
                $contact->setClient($client);
                $contact->setEntreprise($entreprise);
                $manager->persist($contact);
            }
        }

        
        //AUTOMOBILES
        foreach ($tabMarquesAutomobiles as $marqueAuto) {
            for ($a = 0; $a < 5; $a++) {
                $auto = new Automobile();
                $auto->setAnnee($faker->numberBetween(2001, 2022));
                $auto->setModel($faker->numerify('MODEL-####'));
                $auto->setMarque($marqueAuto);
                $auto->setPuissance($faker->numberBetween(8, 20) . "CV");
                $auto->setValeur($faker->numberBetween(1000, 25000));
                $auto->setMonnaie($monnaieUSD);
                $auto->setNbsieges($faker->numberBetween(4, 8));
                $auto->setNature(1);
                $auto->setUtilite(1);
                $auto->setPlaque($faker->randomNumber(4, true) . "BG/0". $a);
                $auto->setChassis("XCD4" . $faker->randomNumber(5, true));
                $auto->setEntreprise($entreprise);
                $manager->persist($auto);
            }
        }

        $manager->flush();
    }
}
