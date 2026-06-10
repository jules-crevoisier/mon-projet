<?php

namespace App\DataFixtures;

use App\Entity\Marque;
use App\Entity\Voiture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $marquesData = [
            'Ferrari' => [
                'anneeCreation' => 1947,
                'paysOrigine' => 'Italie',
                'voitures' => [
                    ['modele' => '488 GTB', 'prix' => '254000.00', 'puissance' => 670, 'anneeSortie' => 2015, 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Ferrari_488_GTB_%28cropped%29.jpg/640px-Ferrari_488_GTB_%28cropped%29.jpg'],
                    ['modele' => 'SF90 Stradale', 'prix' => '575000.00', 'puissance' => 1000, 'anneeSortie' => 2019, 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Ferrari_SF90_Stradale_IMG_0334.jpg/640px-Ferrari_SF90_Stradale_IMG_0334.jpg'],
                ],
            ],
            'Porsche' => [
                'anneeCreation' => 1931,
                'paysOrigine' => 'Allemagne',
                'voitures' => [
                    ['modele' => '911 GT3', 'prix' => '185000.00', 'puissance' => 510, 'anneeSortie' => 2021, 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/19/Porsche_911_GT3_992_IMG_4333.jpg/640px-Porsche_911_GT3_992_IMG_4333.jpg'],
                    ['modele' => 'Taycan Turbo S', 'prix' => '195000.00', 'puissance' => 761, 'anneeSortie' => 2020, 'photo' => null],
                ],
            ],
            'Lamborghini' => [
                'anneeCreation' => 1963,
                'paysOrigine' => 'Italie',
                'voitures' => [
                    ['modele' => 'Huracán EVO', 'prix' => '248000.00', 'puissance' => 640, 'anneeSortie' => 2019, 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Lamborghini_Hurac%C3%A1n_EVO_%28cropped%29.jpg/640px-Lamborghini_Hurac%C3%A1n_EVO_%28cropped%29.jpg'],
                    ['modele' => 'Revuelto', 'prix' => '600000.00', 'puissance' => 1015, 'anneeSortie' => 2023, 'photo' => null],
                ],
            ],
            'McLaren' => [
                'anneeCreation' => 1963,
                'paysOrigine' => 'Royaume-Uni',
                'voitures' => [
                    ['modele' => '720S', 'prix' => '315000.00', 'puissance' => 720, 'anneeSortie' => 2017, 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/McLaren_720S_%28cropped%29.jpg/640px-McLaren_720S_%28cropped%29.jpg'],
                ],
            ],
            'Bugatti' => [
                'anneeCreation' => 1909,
                'paysOrigine' => 'France',
                'voitures' => [
                    ['modele' => 'Chiron', 'prix' => '2500000.00', 'puissance' => 1500, 'anneeSortie' => 2016, 'photo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/60/Bugatti_Chiron_%28cropped%29.jpg/640px-Bugatti_Chiron_%28cropped%29.jpg'],
                ],
            ],
        ];

        foreach ($marquesData as $nom => $data) {
            $marque = (new Marque())
                ->setNom($nom)
                ->setAnneeCreation($data['anneeCreation'])
                ->setPaysOrigine($data['paysOrigine']);

            foreach ($data['voitures'] as $voitureData) {
                $voiture = (new Voiture())
                    ->setModele($voitureData['modele'])
                    ->setPrix($voitureData['prix'])
                    ->setPuissance($voitureData['puissance'])
                    ->setAnneeSortie($voitureData['anneeSortie'])
                    ->setPhoto($voitureData['photo']);

                $marque->addVoiture($voiture);
            }

            $manager->persist($marque);
        }

        $manager->flush();
    }
}
