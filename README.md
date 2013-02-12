BtnMenu
=======

Simple bundle to provide nested menu using yml with extension to twig.

Install:
```json
"require": {
    "bitnoise/menu-bundle": "dev-master"
},
"repositories": [
    {
        "type": "vcs",
        "url":  "git@github.com:Bitnoise/BtnMenuBundle.git"
    }
],
```


Todo:
- create configuration validation
- create abstract menu provider (now works only with yaml)
- make better yaml structure for menu naming convention


Temporary - menu can be created now using following structure in config.yml (or parameters.yml):

```yaml
parameters:
    btn_menu:
        -
            route: create_frame
            name:  Create
            childrens:
                -
                    route: create_frame
                    name: Create end-frame
                -
                    route: queue
                    name: Manage queue
        -
            route: admin_templates
            name:  Admin
```