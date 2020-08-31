<?php
namespace App\Tests\Controller;

use App\Repository\JournalistRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    // ...

    public function testVisitingWhileLoggedIn()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(JournalistRepository::class);

        $testUser = $userRepository->findOneByEmail('test@progres.fr');

        $client->loginUser($testUser);

        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Liste des articles');
    }

    public function testLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();

        $form['email'] = 'test@progres.fr';
        $form['password'] = 'test123';
        $crawler = $client->submit($form);

        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Liste des articles');
    }
}
