<?php

namespace Goldfinch\Loadable\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:loadable-template')]
class LoadableTemplateMakeCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:loadable-template';

    protected $description = 'Create loadable template';

    protected $path = 'themes/[theme]/templates/Loadable';

    protected $type = 'loadable template';

    protected $stub = 'loadable-template.stub';

    protected $extension = '.ss';

    protected function configure(): void
    {
        parent::configure();

        $this->addOption(
            'extrapath',
            null,
            InputOption::VALUE_NONE,
            'Set extra path'
        );
    }

    protected function execute($input, $output): int
    {
        $extrapath = $input->getOption('extrapath');

        if ($extrapath !== false) {
            $this->path = $this->path . '/' . $extrapath;
        }

        if (parent::execute($input, $output) === false) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
