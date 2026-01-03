// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Event;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer des catégories
        $categories = ['Musique', 'Sport', 'Conférence'];
        foreach ($categories as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $this->addReference($name, $category);
        }

        // Créer des événements
        for ($i = 1; $i <= 5; $i++) {
            $event = new Event();
            $event->setTitle("Événement $i");
            $event->setDescription("Description de l'événement $i");
            $event->setCategory($this->getReference($categories[array_rand($categories)]));
            $manager->persist($event);
        }

        $manager->flush();
    }
}
