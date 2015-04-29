<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Controller;

use Nours\RestAdminBundle\Api\ApiEventDispatcher;
use Nours\RestAdminBundle\Api\Event\ApiEvent;
use Nours\RestAdminBundle\Controller\CoreController;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Nours\RestAdminBundle\Tests\Fixtures\Entity\Post;

/**
 * Class CoreControllerTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CoreControllerTest extends AdminTestCase
{
    /**
     * @var CoreController
     */
    private $controller;

    public function setUp()
    {
//        $this->controller = $this->get('rest_admin.core_controller');
    }


    public function testIndexAction()
    {
        $client = $this->getClient();
        $dispatcher = $this->get('rest_admin.core_controller')->getDispatcher();

        $dispatcher->addEventListener('load', function(ApiEvent $event) {
            $event->setModel(array(
                new Post(1),
                new Post(2),
            ));
            $event->stopPropagation();
        });

        $crawler = $client->request('GET', '/posts');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            'post.title.index',
            $client->getResponse()->getContent()
        );
    }
}