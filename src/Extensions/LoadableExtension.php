<?php

namespace Goldfinch\Loadable\Extensions;

use SilverStripe\ORM\DataList;
use SilverStripe\View\SSViewer;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\FieldType\DBHTMLText;

class LoadableExtension extends DataExtension
{
    public function loadableTemplate()
    {
        $fullpath =
            'Loadable/' . str_replace('\\', '/', get_class($this->owner));
        $shortpath = 'Loadable/' . get_class_name(get_class($this->owner));

        if (SSViewer::chooseTemplate($fullpath)) {
            return $this->owner->renderWith($fullpath);
        } else if (SSViewer::chooseTemplate($shortpath)) {
            return $this->owner->renderWith($fullpath);
        } else if (ss_theme_template_file_exists($fullpath)) {
            return $this->owner->renderWith($fullpath);
        } elseif (ss_theme_template_file_exists($shortpath)) {
            return $this->owner->renderWith($shortpath);
        }

        $html = DBHTMLText::create();
        $html->setValue(
            '<p>Template not found. Create one in /themes/' .
                ss_theme() .
                '/<strong>' .
                $fullpath .
                '.ss</strong></p>',
        );
        return $html;
    }

    public static function loadable(DataList $list, HTTPRequest $request, array $data, array $config): DataList
    {
        return $list;
    }
}
