<?php

namespace Goldfinch\Loadable\Providers;

use SilverStripe\View\ArrayData;
use SilverStripe\Control\Director;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Control\Controller;
use SilverStripe\View\TemplateGlobalProvider;

class LoadableTemplateProvider implements TemplateGlobalProvider
{
    public static function get_template_global_variables(): array
    {
        return ['LoadableAs', 'LoadableWith'];
    }

    public static function LoadableAs($class, $id = null, $method = null)
    {
        return self::LoadableFetch($class, $id, $method)->renderWith(
            'Goldfinch/Loadable/LoadmoreBase',
        );
    }

    public static function LoadableWith($class, $id = null, $method = null)
    {
        return self::LoadableFetch($class, $id, $method);
    }

    private static function LoadableFetch($class, int $id = null, $method = null)
    {
        $config = ss_config('Goldfinch\Loadable\Loadable', 'loadable');

        if ($id && $method) {
            if (method_exists($class, $method)) {
                $classMethod = $class . $method; // ! saving $class before it's going to be overwritten

                $list = $class::get()->byID($id);
                $list = $list->$method();

                if (get_class($list) === PaginatedList::class) {
                    $list = $list->getList();
                }

                $class = $list->dataClass;
            }
        }

        if ($config && isset($config[$class])) {
            $config = $config[$class];

            if (!isset($list)) {
                $list = $class::get();
            }

            if (method_exists($class, 'loadable')) {
                if (Controller::has_curr()) {
                    $ctrl = Controller::curr();

                    if ($ctrl) {
                        $request = $ctrl->getRequest();

                        $loadableData = [
                            'urlparams' => $_GET,
                        ];

                        $list = $class::loadable($list, $request, $loadableData, $config);
                    }
                }
            }

            if (isset($config['dbconfig']))
            {
                $configClass = current(array_keys($config['dbconfig']));
                $configFields = current($config['dbconfig']);

                if (method_exists($configClass, 'current_config')) {
                    $cfg = $configClass::current_config();

                    if (isset($configFields['initial_loaded']) && $configFields['initial_loaded']) {
                        $initial_loaded_field = $configFields['initial_loaded'];
                        if ($cfg->$initial_loaded_field) {
                            $initial_loaded = $cfg->$initial_loaded_field;
                        }
                    }
                }
            }

            if (!isset($initial_loaded))
            {
                $initial_loaded = $config['initial_loaded'];
            }

            $returnList = $list->limit($initial_loaded);
            $countRemains = $list->Count() - $returnList->Count();

            $data = new ArrayData([
                'CountRemains' => $countRemains,
                'List' => $returnList,
                'LoadableObject' => app_encrypt($class),
                'LoadableMethod' => isset($classMethod) ? app_encrypt($classMethod) : '',
                'LoadableMethodID' => $id ?? '',
                // 'Opts' => new ArrayData([
                //     'total' => $class::get()->Count(),
                //     'initial_loaded' => $config['initial_loaded'],
                //     'per_each_load' => $config['per_each_load'],
                // ]),
            ]);

            $return = new ArrayData([
                'List' => $data->renderWith('Goldfinch/Loadable/Loadable'),
                'Action' => $data->renderWith(
                    'Goldfinch/Loadable/LoadableAction',
                ),
                'Data' => $data,
            ]);

            return $return;
        }
    }
}
