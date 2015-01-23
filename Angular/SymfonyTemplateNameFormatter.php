<?php

/*
 * This file is part of the AsseticAngularJsBundle package.
 *
 * (c) Pascal Kuendig <padakuro@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asoc\AsseticAngularJsBundle\Angular;

use Assetic\Asset\AssetInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class SymfonyTemplateNameFormatter implements TemplateNameFormatterInterface
{

    /**
     * Bundle map: bundle root => bundle name
     *
     * Used to map asset files to a bundle
     *
     * @var array
     */
    private $bundleMap;
    private $angularModuleName;

    public function __construct(KernelInterface $kernel, $angularModuleName)
    {
        $bundleMap = array();
        foreach ($kernel->getBundles() as $bundle) {
            $bundleMap[$bundle->getPath()] = $bundle->getName();
        }

        $this->bundleMap = $bundleMap;
        $this->angularModuleName = $angularModuleName;
    }

    public function getForAsset(AssetInterface $asset)
    {
        $sourceRoot = $asset->getSourceRoot();
        if (!isset($this->bundleMap[$sourceRoot])) {
            throw new \Exception('Could not map the asset to a bundle');
        }

        // get the relative path
        $templateName = $asset->getSourcePath();

        // process module name by replacing all '$(segments[i])' occurrences in the configured module name
        $segments = explode('/', $templateName);
        $moduleName = preg_replace_callback('/\\$segments\\[(-?\\d+)\\]/', function ($match) use ($segments) {
            $index = intval($match[1]);
            $index = $index < 0 ? count($segments) + $index : $index;
            return ($index >= 0 && $index < count($segments)) ? $segments[$index] : '';
        }, $this->angularModuleName);

        // by convention, all symfony views are in Resources/views/, therefore remove this segment
        $templateName = str_replace('Resources/views/', '', $templateName);
        // remove the .ng extension (our convention)
        $templateName = str_replace('.ng', '', $templateName);
        // prepend bundle name
        $bundleName = $this->bundleMap[$sourceRoot];
        $templateName = sprintf('%s/%s', $bundleName, $templateName);

        return array(
            'moduleName' => $moduleName,
            'templateName' => $templateName,
        );
    }

}
