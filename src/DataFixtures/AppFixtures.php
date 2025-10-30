<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create("fr_FR");

        for($i = 1; $i<= 15; $i++)
        {
            $ad = new Ad();
            $marque = $faker->word();
            $modele = $faker->words(2, true);
            $cover = "https://picsum.photos/id/".$i."/1000/350";
            $randomPrice = mt_rand(500000, 10000000) / 100;
            $c = rand(1,3);
            $carbu = ($c == 1) ? "essence" : (($c == 2) ? "diesel" : "électrique");
            $year = $faker->dateTimeBetween('2000-01-01', '2024-12-31');
            $trans = (rand(1, 2) == 1) ? "manuelle" : "automatique";
            $descr = '<p>'.join('</p><p>',$faker->paragraphs(5)).'</p>';
            $opt = '<p>'.join('</p><p>',$faker->words(5)).'</p>';

            $ad->setMarque($marque)
                ->setModele($modele)
                ->setCover($cover)
                ->setKm(rand(1000, 100000))
                ->setPrice($randomPrice)
                ->setNbOwner(rand(1,5))
                ->setCylindree(rand(500, 1000))
                ->setPower(rand(100, 300))
                ->setCarbu($carbu)
                ->setYear($year)
                ->setTransmission($trans)
                ->setDescri($descr)
                ->setOpt($opt)
            ;
            
            $ad->initializeSlug();

            $manager->persist($ad);

            for($j = 1; $j <= rand(3,6); $j++)
            {
                $image = new Image();
                $image->setAd($ad)
                    ->setUrl("https://picsum.photos/id/".$j."/900")
                    ->setCaption($faker->words(3, true));
                
                $manager->persist($image);
            }

        }

        $manager->flush();
    }
}
