<?php

namespace App\Factory;

use App\Entity\Post;
use DateTimeImmutable;
use Zenstruck\Foundry\Proxy;
use App\Factory\CategoryFactory;
use App\Repository\PostRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\RepositoryProxy;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * @extends ModelFactory<Post>
 *
 * @method static Post|Proxy createOne(array $attributes = [])
 * @method static Post[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Post[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Post|Proxy find(object|array|mixed $criteria)
 * @method static Post|Proxy findOrCreate(array $attributes)
 * @method static Post|Proxy first(string $sortedField = 'id')
 * @method static Post|Proxy last(string $sortedField = 'id')
 * @method static Post|Proxy random(array $attributes = [])
 * @method static Post|Proxy randomOrCreate(array $attributes = [])
 * @method static Post[]|Proxy[] all()
 * @method static Post[]|Proxy[] findBy(array $attributes)
 * @method static Post[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Post[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PostRepository|RepositoryProxy repository()
 * @method Post|Proxy create(array|callable $attributes = [])
 */
final class PostFactory extends ModelFactory
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        parent::__construct();
        $this->slugger = $slugger;

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        $datetime = self::faker()->dateTimeBetween('-3 years', 'now', 'Europe/Paris');
        $dateTimeImmutable = DateTimeImmutable::createFromMutable($datetime);
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'title' => self::faker()->sentence(),
            'content' => self::faker()->text(1000),
            'image' => 'https://picsum.photos/seed/post-' . rand(0,500) . '/750/300',
            // 'author' => self::faker()->name(),
            'createdAt' => $dateTimeImmutable,
            'category' => CategoryFactory::random(),
            'user' => UserFactory::findOrCreate(['email' => 'admin@gmail.com'])
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Post $post): void {})
            ->afterInstantiate(function(Post $post) {
                // On récupère le titre de l'article
                $title = $post->getTitle();

                // On sluggifie ce titre avec le slugger
                $slug = $this->slugger->slug($title);

                // On enregistre ce slug dans le champ slug
                $post->setSlug($slug);
            });

        ;
    }

    protected static function getClass(): string
    {
        return Post::class;
    }
}
