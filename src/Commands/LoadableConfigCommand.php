<?php

namespace Goldfinch\Loadable\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;

#[AsCommand(name: 'vendor:loadable:config')]
class LoadableConfigCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:loadable:config';

    protected $description = 'Create Loadable YML config';

    protected $path = 'app/_config';

    protected $type = 'config';

    protected $stub = './stubs/config.stub';

    protected $extension = '.yml';
}
