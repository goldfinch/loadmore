<?php

namespace Goldfinch\Loadable;

use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;

class Loadable extends Controller
{
    private static $allowed_actions = [
        'fetch',
    ];

    private static $url_handlers = [
        'POST fetch//$stock!' => 'fetch',
    ];

    public function fetch(HTTPRequest $request)
    {
        $stock = $request->param('stock');

        if(!Director::is_ajax() || !$stock)
        {
            return false;
        }

        if (!is_sha1($stock))
        {
            return false;
        }

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        foreach (ss_config(__CLASS__, 'loadable') as $class => $props)
        {
            if ($stock === app_encrypt($class))
            {
                $loadable = [
                    'class' => $class,
                    'props' => $props,
                ];
                break;
            }
        }

        if (isset($loadable))
        {
            $class = $loadable['class'];
            if (method_exists($class, 'loadable'))
            {
                $list = $class::loadable($data, $request, $props);
            }
            else
            {
                $list = $class::get();

            }
        }
        else
        {
            return false;
        }

        $returnList = $list->limit($props['per_each_load'], $data['start']);

        $countRemains = $list->Count() - $returnList->Count() - $data['start'];

        if(!$list->count()) {
            return json_encode(false);
        }

        $LoadedItems = $list->count();

        $data = new ArrayData([
            'CountRemains' => $countRemains,
            'List' => $returnList,
        ]);

        return $this->customise($data)->renderWith('Goldfinch/Loadable/Loadable');
    }
}
