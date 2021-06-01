# Configuring The Bundle

## Default View

The default view for your application can be set with the `default_view` configuration node. This defaults to "default".

```yaml
# config/packages/babdev_pagerfanta.yaml
babdev_pagerfanta:
    default_view: my_view
```

## Default Twig Template

The default Twig template for Twig views in your application can be set with the `default_twig_template` configuration node. This defaults to "`@BabDevPagerfanta/default.html.twig`".

```yaml
# config/packages/babdev_pagerfanta.yaml
babdev_pagerfanta:
    default_view: twig
    default_twig_template: '@App/Pagerfanta/default.html.twig'
```

## Exception Strategies

By default, the bundle converts `Pagerfanta\Exception\NotValidCurrentPageException` and `Pagerfanta\Exception\NotValidMaxPerPageException` exceptions into 404 responses. If you would like to disable or change this behavior, you can change the strategies using the `exceptions_strategy` node by setting the value to "custom" for each behavior you want to change.

```yaml
# config/packages/babdev_pagerfanta.yaml
babdev_pagerfanta:
    exceptions_strategy:
        out_of_range_page: custom # Disables converting `Pagerfanta\Exception\NotValidMaxPerPageException` to a 404 response
        not_valid_current_page: to_http_not_found # Default behavior converting `Pagerfanta\Exception\NotValidCurrentPageException` to a 404 response
```
