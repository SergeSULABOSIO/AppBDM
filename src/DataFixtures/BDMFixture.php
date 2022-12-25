<?php

namespace App\DataFixtures;

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
        // $product = new Product();
        // $manager->persist($product);


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

        //ASSUREURS - ACTIVA
        $activa = new Assureur();
        $activa->setNom("ACTIVA ASSURANCE");
        $activa->setAdresse("Gombe / Kinshasa");
        $activa->setTelephone("+243828727706");
        $activa->setEmail("info@activa.com");
        $activa->setSiteweb("http://www.activa.com");
        $activa->setRccm("RCCM4545454545");
        $activa->setIdnat("IDNAT4545454545");
        $activa->setLicence("LICARCA454545");
        $activa->setNumimpot("IMPO7878784545");
        $activa->setIsreassureur(false);
        $activa->setEntreprise($entreprise);

        $manager->persist($activa);


        //ASSUREURS - RAWSUR
        $rawsure = new Assureur();
        $rawsure->setNom("RAWSUR ASSURANCE");
        $rawsure->setAdresse("Gombe / Kinshasa");
        $rawsure->setTelephone("+243828727706");
        $rawsure->setEmail("info@rawsur.com");
        $rawsure->setSiteweb("http://www.rawsur.com");
        $rawsure->setRccm("RCCM4545454545");
        $rawsure->setIdnat("IDNAT4545454545");
        $rawsure->setLicence("LICARCA454545");
        $rawsure->setNumimpot("IMPO7878784545");
        $rawsure->setIsreassureur(false);
        $rawsure->setEntreprise($entreprise);

        $manager->persist($rawsure);


        //ASSUREURS - SFA
        $sfa = new Assureur();
        $sfa->setNom("SFA CONGO SA");
        $sfa->setAdresse("Gombe / Kinshasa");
        $sfa->setTelephone("+243828727706");
        $sfa->setEmail("info@sfa.com");
        $sfa->setSiteweb("http://www.sfa.com");
        $sfa->setRccm("RCCM4545454545");
        $sfa->setIdnat("IDNAT4545454545");
        $sfa->setLicence("LICARCA454545");
        $sfa->setNumimpot("IMPO7878784545");
        $sfa->setIsreassureur(false);
        $sfa->setEntreprise($entreprise);

        $manager->persist($sfa);


        //ASSUREURS - SUNU
        $sunu = new Assureur();
        $sunu->setNom("SUNU ASSURANCE IARD");
        $sunu->setAdresse("Gombe / Kinshasa");
        $sunu->setTelephone("+243828727706");
        $sunu->setEmail("info@sunu.com");
        $sunu->setSiteweb("http://www.sunu.com");
        $sunu->setRccm("RCCM4545454545");
        $sunu->setIdnat("IDNAT4545454545");
        $sunu->setLicence("LICARCA454545");
        $sunu->setNumimpot("IMPO7878784545");
        $sunu->setIsreassureur(false);
        $sunu->setEntreprise($entreprise);

        $manager->persist($sunu);


        //ASSUREURS - MAYFAIR
        $mayfair = new Assureur();
        $mayfair->setNom("MAYFAIR INSURANCE COMPANY RDC SARL");
        $mayfair->setAdresse("Gombe / Kinshasa");
        $mayfair->setTelephone("+243828727706");
        $mayfair->setEmail("info@mayfair.com");
        $mayfair->setSiteweb("http://www.mayfair.com");
        $mayfair->setRccm("RCCM4545454545");
        $mayfair->setIdnat("IDNAT4545454545");
        $mayfair->setLicence("LICARCA454545");
        $mayfair->setNumimpot("IMPO7878784545");
        $mayfair->setIsreassureur(false);
        $mayfair->setEntreprise($entreprise);

        $manager->persist($mayfair);



        $monnaieCDF = new Monnaie();
        $monnaieCDF->setNom("Franc Congolais");
        $monnaieCDF->setCode("CDF");
        $monnaieCDF->setTauxusd(2050);
        $monnaieCDF->setIslocale(true);
        $monnaieCDF->setEntreprise($entreprise);

        $manager->persist($monnaieCDF);


        $monnaieUSD = new Monnaie();
        $monnaieUSD->setNom("Dollars Américains");
        $monnaieUSD->setCode("USD");
        $monnaieUSD->setTauxusd(1);
        $monnaieUSD->setIslocale(false);
        $monnaieUSD->setEntreprise($entreprise);

        $manager->persist($monnaieUSD);


        $client = new Client();
        $client->setNom("Bolloré Transport et Logistics RDC");
        $client->setAdresse("Limeté - KINSHASA / RDC");
        $client->setTelephone("+243828727706");
        $client->setEmail("info@bollore.com");
        $client->setSiteweb("http://www.bollore.com");
        $client->setIspersonnemorale(true);
        $client->setRccm("RCCM4545454545");
        $client->setIdnat("ID47878787878");
        $client->setNumipot("IMP4787878000");
        $client->setSecteur(0);
        $client->setEntreprise($entreprise);

        $manager->persist($client);



        $client2 = new Client();
        $client2->setNom("Serge SULA BOSIO");
        $client2->setAdresse("Limeté - KINSHASA / RDC");
        $client2->setTelephone("+243828727706");
        $client2->setEmail("ssula@aib-brokers.com");
        $client2->setSiteweb("http://www.tuminvite.com");
        $client2->setIspersonnemorale(false);
        $client2->setRccm("");
        $client2->setIdnat("");
        $client2->setNumipot("");
        $client2->setSecteur(0);
        $client2->setEntreprise($entreprise);

        $manager->persist($client2);


        for ($i = 0; $i < 100; $i++) {
            $client3 = new Client();
            $client3->setNom($i."_CLIENT".$i);
            $client3->setAdresse($i."_Adresse".$i);
            $client3->setTelephone("+24382872770".$i);
            $client3->setEmail("cli".$i."@gmail.com");
            $client3->setSiteweb("http://www.cli" . $i . ".com");
            $client3->setIspersonnemorale(false);
            $client3->setRccm("");
            $client3->setIdnat("");
            $client3->setNumipot("");
            $client3->setSecteur(0);
            $client3->setEntreprise($entreprise);

            $manager->persist($client3);
        }



        $produit = new Produit();
        $produit->setNom("INCENDIE ET RISQUES DIVERS");
        $produit->setDescription("Couvre les risques liés à l'incendie, dégâts des eaux, chuttes de corps aéronef, éléments naturels et autres.");
        $produit->setIsobligatoire(true);
        $produit->setIsabonnement(false);
        $produit->setTauxarca(0.10);
        $produit->setEntreprise($entreprise);

        $manager->persist($produit);



        $partenaire = new Partenaire();
        $partenaire->setNom("AFINBRO");
        $partenaire->setAdresse("Zambie");
        $partenaire->setEmail("info@afinbro.com");
        $partenaire->setSiteweb("http://www.afinbro.com");
        $partenaire->setRccm("RCCM44787878");
        $partenaire->setIdnat("IDNAT78787844");
        $partenaire->setNumimpot("IMP88878545400");
        $partenaire->setPart(0.50);
        $partenaire->setEntreprise($entreprise);

        $manager->persist($partenaire);


        $taxeTVA = new Taxe();
        $taxeTVA->setNom("TVA");
        $taxeTVA->setDescription("Taxe sur la Valeur Ajoutée");
        $taxeTVA->setTaux(0.16);
        $taxeTVA->setOrganisation("DGI");
        $taxeTVA->setEntreprise($entreprise);

        $manager->persist($taxeTVA);


        $taxeARCA = new Taxe();
        $taxeARCA->setNom("ARCA");
        $taxeARCA->setDescription("Frais de surveillance ARCA");
        $taxeARCA->setTaux(0.02);
        $taxeARCA->setOrganisation("ARCA");
        $taxeARCA->setEntreprise($entreprise);

        $manager->persist($taxeARCA);


        $contactSerge = new Contact();
        $contactSerge->setNom("Mme. Joelle SULA FALA");
        $contactSerge->setPoste("Directrice Admin & Financière");
        $contactSerge->setTelephone("+243828727706");
        $contactSerge->setEmail("joellefala1@gmail.com");
        $contactSerge->setClient($client);
        $contactSerge->setEntreprise($entreprise);

        $manager->persist($contactSerge);


        $auto = new Automobile();
        $auto->setAnnee("2001");
        $auto->setModel("RAV4 Intermédiaire");
        $auto->setMarque("TOYOTA");
        $auto->setPuissance("9CV");
        $auto->setValeur(7600);
        $auto->setMonnaie($monnaieUSD);
        $auto->setNbsieges(5);
        $auto->setNature(1);
        $auto->setUtilite(1);
        $auto->setPlaque("6087BJ/01");
        $auto->setChassis("XCD4545114711455877770");
        $auto->setEntreprise($entreprise);

        $manager->persist($auto);


        $manager->flush();
    }
}
