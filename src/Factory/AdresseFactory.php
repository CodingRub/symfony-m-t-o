<?php

namespace App\Factory;

use App\Entity\Adresse;
use App\Factory\UserFactory;
use App\Repository\AdresseRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Adresse>
 *
 * @method        Adresse|Proxy create(array|callable $attributes = [])
 * @method static Adresse|Proxy createOne(array $attributes = [])
 * @method static Adresse|Proxy find(object|array|mixed $criteria)
 * @method static Adresse|Proxy findOrCreate(array $attributes)
 * @method static Adresse|Proxy first(string $sortedField = 'id')
 * @method static Adresse|Proxy last(string $sortedField = 'id')
 * @method static Adresse|Proxy random(array $attributes = [])
 * @method static Adresse|Proxy randomOrCreate(array $attributes = [])
 * @method static AdresseRepository|RepositoryProxy repository()
 * @method static Adresse[]|Proxy[] all()
 * @method static Adresse[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Adresse[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Adresse[]|Proxy[] findBy(array $attributes)
 * @method static Adresse[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Adresse[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class AdresseFactory extends ModelFactory
{
    private $client;
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(HttpClientInterface $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        $adresse = self::faker()->streetAddress();
        $response = $this->client->request('GET',
            "https://api-adresse.data.gouv.fr/search/?q=$adresse", [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

        $respArray = $response->toArray();
        return [
            'adresse' => $respArray["features"][0]["properties"]["name"],
            'author' => UserFactory::random(),
            'cp' => $respArray["features"][0]["properties"]["postcode"],
            'latitude' => $respArray["features"][0]["geometry"]["coordinates"][1],
            'longitude' => $respArray["features"][0]["geometry"]["coordinates"][0],
            'ville' => $respArray["features"][0]["properties"]["city"],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Adresse $adresse): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Adresse::class;
    }
}
