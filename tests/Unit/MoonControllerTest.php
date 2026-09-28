<?php

namespace ApplicationTest\Controller;

use Application\Controller\MoonController;
use App\Entity\Moon;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Laminas\Http\Request;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\RouteMatch;

class MoonControllerTest extends TestCase
{
    private MoonController $controller;
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->controller = new MoonController($this->entityManager);
    }

    public function testListActionReturnsJsonModel(): void
    {
        $request = new Request();
        $routeMatch = new RouteMatch(['action' => 'list']);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($routeMatch);

        $this->controller->setEvent($event);

        $result = $this->controller->listAction();

        $this->assertIsArray($result->getVariables());
        $this->assertArrayHasKey('status', $result->getVariables());
    }
}
