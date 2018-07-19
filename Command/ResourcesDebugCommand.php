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
use Nours\RestAdminBundle\Domain\DomainResource;
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

    private function dumpResourceLight(OutputInterface $output, DomainResource $resource)
    {
        $output->writeln("\t<info>".$resource->getFullName().'</info> : ' . $resource->getClass());
    }

    private function dumpResource(InputInterface $input, OutputInterface $output, DomainResource $resource)
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
                    foreach ($this->sortHandlers($value) as $handler) {
                        if ($first) {
                            $first = false;
                        } else {
                            $output->write('           ');
                        }
                        $function = $handler[0];
                        $priority = $handler[1];
                        $output->writeln("<info>[" . $priority . "] - " . (is_array($function) ?
                                ((is_object($function[0]) ? get_class($function[0]) : $function[0]). ':' . $function[1]) :
                                $function) . '</info>');
                    }
                }
            } else {
                $this->writeVal($output, $name, $value);
            }
        }

        $output->writeLn('Route name : <info>' . $action->getRouteName() . '</info>');
        $output->writeLn('Form action route name : <info>' . $action->getFormActionRouteName() . '</info>');
    }

    private function writeVal(OutputInterface $output, $label, $value)
    {
        $output->writeln($label . ' : <info>'.$this->escape($value).'</info>');
    }

    private function sortHandlers($handlers)
    {
        usort($handlers, function($h1, $h2) {
            return $h1[1] - $h2[1];
        });

        return $handlers;
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