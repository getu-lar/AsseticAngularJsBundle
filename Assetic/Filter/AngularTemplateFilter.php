<?php

/*
 * This file is part of the AsseticAngularJsBundle package.
 *
 * (c) Pascal Kuendig <padakuro@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asoc\AsseticAngularJsBundle\Assetic\Filter;

use Asoc\AsseticAngularJsBundle\Angular\TemplateNameFormatterInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Filter\BaseNodeFilter;

/**
 * Compile AngularJS templates for $templateCache.
 *
 * @link http://angularjs.com/
 */
class AngularTemplateFilter extends BaseNodeFilter
{
    /**
     * @var \Asoc\AsseticAngularJsBundle\Angular\TemplateNameFormatterInterface
     */
    private $templateNameFormatter;

    public function __construct(TemplateNameFormatterInterface $templateNameFormatter)
    {
        $this->templateNameFormatter = $templateNameFormatter;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $template = $this->templateNameFormatter->getForAsset($asset);

        $content = addslashes($asset->getContent());
        $html = '';
        // Explode by EOL
        $content = preg_split("/\R/", $content);
        foreach ($content as $line) {
            if ($html !== '') {
                $html .= "\n +";
            }
            $html .= sprintf('"%s"', $line);
        }

        $js = <<<JS
(function (angular) {
var m;
try { m = angular.module("{$template['moduleName']}"); } catch(err) { m = angular.module("{$template['moduleName']}", []); }
m.run(["\$templateCache", function(\$templateCache) {
  \$templateCache.put("{$template['templateName']}", $html);
}]);
})(angular);
JS;

        $asset->setContent($js);
    }
}
