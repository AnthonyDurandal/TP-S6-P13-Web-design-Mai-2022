<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{

    private $ARTICLE_NUMBER = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR'); 

        // $product = new Product();
        // $manager->persist($product);

        $admin = new User();
            $admin->setEmail('admin@gmail.com');
            $admin->setPassword('mdp');
        $manager->persist($admin);


        $category_accident = new Category();
            $category_accident->setEntitled('accident');

        $category_politique = new Category();
            $category_politique->setEntitled('politique');

        $categories = [$category_accident, $category_politique];

        foreach ($categories as $category) {
            $manager->persist($category);
        }



        for ($article_id=0; $article_id < $this->ARTICLE_NUMBER; $article_id++) { 
            $article = new Article();
                $article->setTitle('incendies-australie-accident-rechauffement-climatique-actu');
                $article->setHeader('De nombreux incendies en australie');
                $article->setAuthorName($faker->name);
                $article->setCategory($category_accident);
                $article->setUrlPath('url path euh');
                $article->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now', $timezone = null)));
                $article->setContent('
Les <strong>incendies</strong> australiens, nommés bushfire ou feux de brousse, suscitent un battage médiatique permanent, fondé en grande partie sur une cause ostentatoire et considérée comme une évidence : le « réchauffement climatique ».

Au-delà de ces annonces le plus souvent teintées d’idéologie, une lecture plus attentive et objective de la réalité révèle une multiplicité de causes que les médias s’abstiennent d’évoquer la plupart du temps.

En 2017, j’avais eu l’occasion d’écrire dans les lignes de Contrepoints l’amalgame qui était fait entre les importants <strong>incendies</strong> dans le Sud-Est, et l’interprétation de leur origine… à savoir l’indéniable « réchauffement climatique anthropique ».

De la même façon, je vous propose une lecture objective de la problématique et très synthétique, car le sujet est vaste, des <strong>incendies australiens</strong>.

Des <strong>incendies</strong> majeurs
On ne peut nier la catastrophe que vit l’<strong>Australie</strong> depuis deux ans, puisque ces <strong>incendies</strong> majeurs ont débuté en 2018. Ils ne sont pas tous situés dans l’Est australien, mais également dans le Nord-Est, le Nord, et le Sud-Ouest, les plus importants se trouvant néanmoins en janvier 2020 dans la partie est des Nouvelles-Galles du Sud.

Il s’agit d’un phénomène naturel extrême, amplifié par les conditions météorologiques, ainsi que par d’autres causes que nous allons examiner.
                ');
                $splitByDot = explode('.',$article->getContent());
                $article->setSumUp($splitByDot[0].'. '.$splitByDot[1].' ...');
                $manager->persist($article);
        }

        $manager->flush();
    }
}
