<?php

namespace Goldfinch\Loadable\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'vendor:loadable:config')]
class LoadableConfigCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:loadable:config';

    protected $description = 'Create Loadable YML config';

    protected $path = 'app/_config';

    protected $type = 'config';

    protected $stub = './stubs/config.stub';

    protected $extension = '.yml';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
