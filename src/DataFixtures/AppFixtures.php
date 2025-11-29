<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Marque;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {}

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create("fr_FR");

        $admin = new User();
        $admin->setFirstName("admin")
            ->setLastName("admin")
            ->setPicture("")
            ->setEmail("admin@myepse.be")
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'passwordAdmin'))
            ->setRoles(['ROLE_ADMIN']);
        
        $manager->persist($admin);

        $genres = ['male', 'femelle'];

        for($u=1; $u<=5; $u++)
        {
            $user = new User();
            $genre = $faker->randomElement($genres);

            // $picture = "https://randomuser.me/api/portraits/";
            // $pictureId = $faker->numberBetween(1,99).'.jpg';
            // $picture .= ($genre == 'male' ? 'men/' : 'women/').$pictureId;

            $hash = $this->passwordHasher->hashPassword($user,'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
                ->setPassword($hash)
                ->setPicture("");

            $manager->persist($user);
        }

        for($cpt = 1; $cpt <= 10; $cpt++)
        {
            $marque = new Marque();
            $marque->setName($faker->company);

            $manager->persist($marque);

            for($i = 1; $i<= rand(2,4); $i++)
            {
                $ad = new Ad();
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
        }


        $manager->flush();
    }
}
