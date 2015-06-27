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
 * Debug resources command.
 *
 * Without arguments, prints all available resources.
 *
 * The first argument is the resource name, the command will dump it's actions.
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
            $count = count($resources);
            $output->writeln("\n" . $count . " resource" . ($count > 1 ? 's' : '' ) . " are available :\n");

            foreach ($resources as $resource) {
                $this->dumpResourceLight($output, $resource);
            }

            $output->writeln("");
        }
    }

    private function dumpResourceLight(OutputInterface $output, Resource $resource)
    {
        $output->writeln("\t<info>".$resource->getFullName().'</info> : ' . $resource->getClass());
    }

    private function dumpResource(InputInterface $input, OutputInterface $output, Resource $resource)
    {
        $this->dumpResourceLight($output, $resource);
        foreach ($resource->getConfigs() as $name => $value) {
            $this->writeVal($output, $name, $value);
        }

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
        $output->writeln("\nAction <info>" . $action->getName() . '</info>');
        $output->writeln(str_repeat('*', 7 + strlen($action->getName())));

        foreach ($action->getConfigs() as $name => $value) {
            if ($name == 'handlers') {
                if ($value) {
                    $output->write('Handlers : ');
                    $first = true;
                    foreach ($value as $handler) {
                        if ($first) {
                            $first = false;
                        } else {
                            $output->write('           ');
                        }
                        $output->writeln("<info>" . (is_array($handler) ?
                                ((is_object($handler[0]) ? get_class($handler[0]) : $handler[0]). ':' . $handler[1]) :
                                $handler) . '</info>');
                    }
                }
            } else {
//                //
//                $method = 'get' . ucfirst($name);
//                if (method_exists($action, $method)) {
//                    $value = $action->$method;
//                }

                $this->writeVal($output, $name, $value);
            }
        }
    }

    private function writeVal(OutputInterface $output, $label, $value)
    {
        $output->writeln($label . ' : <info>'.$this->escape($value).'</info>');
    }

    private function escape($value)
    {
        if (is_array($value)) {
            return '[' . implode(', ', array_map(array($this, 'escape'), $value)) . ']';
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            return 'null';
        } else {
            return $value;
        }
    }
}