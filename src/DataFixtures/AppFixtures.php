<?php

namespace App\DataFixtures;

use App\Entity\Entreprise;
use App\Entity\Formation;
use App\Entity\Stage;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //Création de 2 utilisateurs
        $dorian = new User();
        $dorian->setPrenom("Dorian");
        $dorian->setNom("Nunez");
        $dorian->setEmail("dorian@admin.fr");
        $dorian->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $dorian->setPassword('$2y$10$yI2lj5FdY3WkX.9pGOlbvOG5TdKSQlz3mX7BIxAwL6Hqbd/fFO.MG');
        $manager->persist($dorian);

        $luc = new User();
        $luc->setPrenom("Luc");
        $luc->setNom("Leydert");
        $luc->setEmail("luc@user.fr");
        $luc->setRoles(['ROLE_USER']);
        $luc->setPassword('$2y$10$Wm0O9bmQ3dODbladOGstNu.3bsAPhodVdq/4bieUlgtwnLCl1HfHy');
        $manager->persist($luc);


        $faker = \Faker\Factory::create('fr_FR');


        for ($i = 0; $i < 20; $i++) {
            $nom = $faker->company;
            $entreprise = new Entreprise();
            $entreprise->setNom($nom);
            $entreprise->setActivite($faker->realText());
            $entreprise->setSite("https://www.$nom.com"); // $faker->url ne marchait pas, il y avait l'erreur "join(): Argument #2 ($array) must be of type ?array, string given"
            $entreprise->setAdresse($faker->address);

            $entreprises[] = $entreprise;
            $manager->persist($entreprise);
        }


        $modulesFormation = array(
            "DUT Info" => "Diplôme Universitaire de Technologique Informatique",
            "LP Multimédia" => "Licence Professionelle Multimédia",
            "DUT IC" => "Diplôme Universitaire de Technologique Information et Communication"
        );

        foreach ($modulesFormation as $nomC => $nomL) {
            $formation = new Formation();
            $formation->setNom($nomC);
            $formation->setNomComplet($nomL);

            for ($numStage = 0; $numStage < 10; $numStage++) {
                $stage = new Stage();
                $stage->setTitre($faker->realText(40));
                $stage->setContact($faker->email);
                $stage->setMissions($faker->realText());

                $stage->addFormation($formation);

                $numEntreprise = $faker->numberBetween($min = 0, $max = 19);

                $stage->setEntreprise($entreprises[$numEntreprise]);
                $entreprises[$numEntreprise]->addStage($stage);

                $manager->persist($stage);
                $manager->persist($entreprises[$numEntreprise]);
            }
            $manager->persist($formation);
        }


        $manager->flush();
    }
}
