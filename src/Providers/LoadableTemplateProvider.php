<?php

namespace Goldfinch\Loadable\Providers;

use SilverStripe\View\ArrayData;
use SilverStripe\View\TemplateGlobalProvider;

class LoadableTemplateProvider implements TemplateGlobalProvider
{
    public static function get_template_global_variables(): array
    {
        return [
            'Loadable',
            'LoadableWith',
        ];
    }

    public static function Loadable($class)
    {
        return self::LoadableFetch($class)->renderWith('Goldfinch/Loadable/LoadmoreBase');
    }

    public static function LoadableWith($class)
    {
        return self::LoadableFetch($class);
    }

    private static function LoadableFetch($class)
    {
        $config = ss_config('Goldfinch\Loadable\Loadmore', 'loadable');

        if ($config && isset($config[$class]))
        {
            $config = $config[$class];

            $list = $class::get();

            $returnList = $list->limit($config['initial_loaded']);
            $countRemains = $list->Count() - $returnList->Count();

            $data = new ArrayData([
                'CountRemains' => $countRemains,
                'List' => $returnList,
                'LoadableObject' => app_encrypt($class),
                // 'Opts' => new ArrayData([
                //     'total' => $class::get()->Count(),
                //     'initial_loaded' => $config['initial_loaded'],
                //     'per_each_load' => $config['per_each_load'],
                // ]),
            ]);

            $return = new ArrayData([
                'List' => $data->renderWith('Goldfinch/Loadable/Loadable'),
                'Action' => $data->renderWith('Goldfinch/Loadable/LoadableAction'),
                'Data' => $data,
            ]);

            return $return;
        }
    }
}
