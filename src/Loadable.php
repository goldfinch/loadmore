<?php

namespace Goldfinch\Loadable;

use SilverStripe\View\ArrayData;
use SilverStripe\Control\Director;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;

class Loadable extends Controller
{
    private static $allowed_actions = ['fetch'];

    private static $url_handlers = [
        'POST fetch//$stock!' => 'fetch',
    ];

    public function fetch(HTTPRequest $request)
    {
        $stock = $request->param('stock');

        if (!Director::is_ajax() || !$stock) {
            return false;
        }

        if (!is_sha1($stock)) {
            return false;
        }

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        foreach (ss_config(__CLASS__, 'loadable') as $class => $props) {
            if ($stock === app_encrypt($class)) {
                $loadable = [
                    'class' => $class,
                    'props' => $props,
                ];
                break;
            }
        }

        if (isset($loadable)) {

            if (isset($data['substance']) && isset($data['substance_id']) && isset($loadable['props']['bridge'])) {
                foreach ($loadable['props']['bridge'] as $class => $method) {
                    if ($data['substance'] === app_encrypt($class . $method)) {
                        $loadableMethod = [
                            'class' => $class,
                            'method' => $method,
                        ];
                        $data['substance_id'] = (int) $data['substance_id'];
                        break;
                    }
                }
            }

            $class = $loadable['class'];

            if (isset($loadableMethod) && isset($data['substance_id']) && is_numeric($data['substance_id'])) {
                $subClass = $loadableMethod['class'];
                $subMethod = $loadableMethod['method'];
                $list = $subClass::get()->byID($data['substance_id'])->$subMethod();
                if (get_class($list) === PaginatedList::class) {
                    $list = $list->getList();
                }
            } else {
                $list = $class::get();
            }

            if (method_exists($class, 'loadable')) {

                if (isset($data['urlparams']) && $data['urlparams']) {

                    if ($data['urlparams'][0] == '?')
                    {
                        $data['urlparams'] = substr($data['urlparams'], 1);
                    }

                    parse_str($data['urlparams'], $data['urlparams']);
                }

                $list = $class::loadable($list, $request, $data, $props);
            }
        } else {
            return false;
        }

        $returnList = $list->limit($props['per_each_load'], $data['start']);

        $countRemains = $list->Count() - $returnList->Count() - $data['start'];

        if (!$list->count()) {
            return json_encode(false);
        }

        $LoadedItems = $list->count();

        $data = new ArrayData([
            'CountRemains' => $countRemains,
            'List' => $returnList,
        ]);

        return $this->customise($data)->renderWith(
            'Goldfinch/Loadable/Loadable',
        );
    }
}
