<?php
/*
 * This file is part of AdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Doctrine;

use Nours\RestAdminBundle\Doctrine\DoctrineRepository;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Nours\RestAdminBundle\Tests\Fixtures\Entity\Post;
use Nours\RestAdminBundle\Tests\Fixtures\Stub\PostResource;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RepositoryTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DoctrineRepositoryTest extends AdminTestCase
{
    /**
     * @var DoctrineRepository
     */
    private $subject;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    public function setUp()
    {
        $this->manager = $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repository);

        $this->subject = new DoctrineRepository($this->manager);
    }

    public function testFind()
    {
        $result = array(
            'id' => 1
        );

        $stub = new Post(1);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo($result))
            ->willReturn($stub);

        $resource = new PostResource();
        $request = new Request();
        $request->attributes->set('post', 1);

        $result = $this->subject->find($resource, $request);

        $this->assertSame($stub, $result);
    }
}