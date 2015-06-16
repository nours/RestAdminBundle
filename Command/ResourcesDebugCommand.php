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

use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourcesDebugCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('debug:rest_admin')
            ->addArgument('resource', InputArgument::OPTIONAL)
            ->addArgument('action', InputArgument::OPTIONAL)
            ->setDescription('Dumps resources and their actions')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command lists resources and its actions :

  <info>php %command.full_name%</info>
  <info>php %command.full_name% <resource></info>
  <info>php %command.full_name% <resource> <action></info>
EOF
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('rest_admin.manager');

        $resources = $manager->getResourceCollection();

        if ($resourceName = $input->getArgument('resource')) {
            $this->dumpResource($input, $output, $resources->get($resourceName));
        } else {
            foreach ($resources as $resource) {
                $this->dumpResource($input, $output, $resource);
            }
        }
    }

    private function dumpResource(InputInterface $input, OutputInterface $output, Resource $resource)
    {
        $length = strlen($resource->getFullName()) + 2;
        $output->writeln('<info>'.str_repeat('=', $length).'</info>');
        $output->writeln('<info>'.$resource->getFullName().' :</info>');
        $output->writeln('<info>'.str_repeat('=', $length).'</info>');

        if ($actionName = $input->getArgument('action')) {
            $this->dumpAction($output, $resource->getAction($actionName));
        } else {
            foreach ($resource->getActions() as $action) {
                $this->dumpAction($output, $action);
            }
        }

        $output->writeln('');
    }

    private function dumpAction(OutputInterface $output, Action $action)
    {
        $output->writeln("Action <info>" . $action->getName() . '</info>');

        $output->writeln('Controller : <comment>'.$action->getController().'</comment>');
        $output->writeln('Template : <comment>'.$action->getTemplate().'</comment>');
        if ($form = $action->getForm()) {
            $output->writeln('Form : <comment>'.$form.'</comment>');
        }

        if ($handlers = $action->getHandlers()) {
            $output->writeln('Handlers :');
            foreach ($handlers as $handler) {
                $output->writeln("<comment>" . is_array($handler) ? '<array>' : $handler . '</comment>');
            }
        }

        $output->writeln('');
    }
}