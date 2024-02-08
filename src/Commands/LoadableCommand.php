<?php

namespace Goldfinch\Loadable\Commands;

use Goldfinch\Taz\Services\InputOutput;
use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(name: 'loadable')]
class LoadableCommand extends GeneratorCommand
{
    protected static $defaultName = 'loadable';

    protected $description = 'Makes your model loadable';

    protected $no_arguments = true;

    protected function execute($input, $output): int
    {
        $className = $this->askClassNameQuestion('What [model class name] do we need to make loadable? (eg: Article, App\Models\BlogPost)', $input, $output);

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'How to extend this model?',
            ['via extension (recommended)', 'manually'],
            0,
        );
        $question->setErrorMessage('The selection %s is invalid.');
        $extensionType = $helper->ask($input, $output, $question);

        $initial_loaded = $this->askStringQuestion('Set amount of initially loaded items (10 by default):', $input, $output, 10);
        $per_each_load = $this->askStringQuestion('Set amount of loaded items per further load (10 by default):', $input, $output, 10);

        $loadableOptions = [
            'initial_loaded' => (int) $initial_loaded,
            'per_each_load' => (int) $per_each_load,
        ];

        $bridge = $this->askStringQuestion('Set up bridge? (y/n)', $input, $output, 'n');

        if ($bridge == 'y' || $bridge == 'Y') {
            $bridge_className = $this->askClassNameQuestion('What [class name] is going to be used for this bridge (eg: Article, App\Models\BlogPost)', $input, $output);
            $bridge_methodName = $this->askStringQuestion('What [method name] is going to be used for this bridge (eg: List, Posts)', $input, $output);

            if ($bridge_className && $bridge_methodName) {
                $loadableOptions['bridge'] = [
                    $bridge_className => $bridge_methodName,
                ];
            }
        }

        $dbconfig = $this->askStringQuestion('Set up db config? (y/n)', $input, $output, 'n');

        if ($dbconfig == 'y' || $dbconfig == 'Y') {
            $dbconfig_className = $this->askClassNameQuestion('What [class name] of the config to use? (eg: MyConfig, App\Configs\MyConfig)', $input, $output);
            $dbconfig_initial_loaded = $this->askStringQuestion('Set represent config field for [initial_loaded]', $input, $output);
            $dbconfig_per_each_load = $this->askStringQuestion('Set represent config field for [per_each_load]', $input, $output);

            if ($dbconfig_className && $dbconfig_initial_loaded && $dbconfig_per_each_load) {
                $loadableOptions['dbconfig'] = [
                    $dbconfig_className => [
                        'initial_loaded' => $dbconfig_initial_loaded,
                        'per_each_load' => $dbconfig_per_each_load,
                    ],
                ];
            }
        }

        // find config
        $config = $this->findYamlConfigFileByName('app-loadable');

        // create new config if not exists
        if (!$config) {

            $command = $this->getApplication()->find('make:config');
            $command->run(new ArrayInput([
                'name' => 'loadable',
                '--plain' => true,
                '--after' => 'goldfinch/loadable',
                '--nameprefix' => 'app-',
            ]), $output);

            $config = $this->findYamlConfigFileByName('app-loadable');
        }

        // update config
        $this->updateYamlConfig(
            $config,
            'Goldfinch\Loadable\Loadable' . '.loadable.' . $className,
            $loadableOptions,
        );

        if ($extensionType == 'manually') {
            $io = new InputOutput($input, $output);
            $io->display('For manual setup please, refer to the documentation: https://github.com/goldfinch/loadable?tab=readme-ov-file#4-prepare-your-loadable-model');
        } else {

            $config = $this->findYamlConfigFileByName('app-loadable');

            $this->updateYamlConfig(
                $config,
                $className . '.extensions',
                'Goldfinch\Loadable\Extensions\LoadableExtension',
            );
        }

        return Command::SUCCESS;
    }
}
