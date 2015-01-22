AsseticAngularJsBundle
======================
Simple Assetic filter to feed the *$templateCache*.

# Installation

In composer.json
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/getu-lar/AsseticAngularJsBundle"
        }
    ],
    "require": {
        "getu-lar/assetic-angular-js-bundle": "dev-master"
    }
}
```

## Requirements
Any Symfony2 2.3+ application will do.

# Configuration
```yml
asoc_assetic_angular_js:
  angular_module_name: 'mySinglePageApp'
```
Name of angular application name which will be used in template cache (angular.module("mySinglePageApp").run(["$templateCache", function($templateCache) ...).

A special syntax is available in the module name to incorporate parts of the original asset path into the module name. The module path is split into segments - e.g. 'Resources/app/mymodule/somefile.js' is split into segments \[ 'Resources', 'app', 'mymodule', 'somefile.js' \] and syntax of the form `$segments[3]` can be used to interpolate the matching segment into the generated module name.

With the example above, a module name configuration of `my-mod-$segments[3]` will be translated into `my-mod-mymodule`.

# Usage
Just include the Angular templates as any other javascript resource using the javascripts Twig helper and apply the *angular* filter to them.

```twig
{% javascripts filter="angular"
    '@BundleName/Resources/views/aTemplate.html.ng'
    '@BundleName/Resources/views/fooTemplate.html.ng'
    '@BundleName/Resources/views/moarTemplates/*.html.ng'
    %}
    <script type="text/javascript" src="{{ asset_url }}"></script>
{% endjavascripts %}
```

The resulting output will be something like this:

```javascript
angular.module("BundleName/aTemplate.html", []).run(["$templateCache", function($templateCache) {
  $templateCache.put("BundleName/aTemplate.html", "HTML here");
}]);
angular.module("BundleName/fooTemplate.html", []).run(["$templateCache", function($templateCache) {
  $templateCache.put("BundleName/fooTemplate.html", "HTML here");
}]);
angular.module("BundleName/moarTemplates/bar.html", []).run(["$templateCache", function($templateCache) {
  $templateCache.put("BundleName/moarTemplates/bar.html", "HTML here");
}]);
// ...
```

The **.ng** extension is just a convention and can be changed at will. Also, the removal of the **Resources/views/** part is just by the symfony2 convention which can be changed by implementing a custom template name formatter. Now, to use the template a dependency on the module name must be set and after that the template can be retrieved using the templates URL:

```html
<div data-ng-include="BundleName/moarTemplates/bar.html"></div>
```

Of course, wherever a template URL can be specified, the above will work as it is in the default AngularJS template cache.

# License
MIT
