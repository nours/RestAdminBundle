<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Command;

use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResourcesDebugCommand
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourcesDebugCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('nours_rest:debug');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('rest_admin.manager');

        $resources = $manager->getResourceCollection();

        foreach ($resources as $resource) {
            $this->dumpResource($output, $resource);
        }
    }

    private function dumpResource(OutputInterface $output, Resource $resource)
    {
        $output->writeln('<info>'.$resource->getFullName().'</info> :');

//        if ($parent = $resource->getParent()) {
//            $output->writeln("  parent : " . $parent->getFullName());
//        }
        $output->writeln("  <error>actions</error> : ");

        foreach ($resource->getActions() as $action) {
            $output->writeln("    " . $action->getName());
        }

    }
}