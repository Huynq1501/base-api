[![Latest Stable Version](http://poser.pugx.org/nguyenanhung/template-backend-package/v)](https://packagist.org/packages/template-backend-package) [![Total Downloads](http://poser.pugx.org/nguyenanhung/template-backend-package/downloads)](https://packagist.org/packages/nguyenanhung/template-backend-package) [![Latest Unstable Version](http://poser.pugx.org/nguyenanhung/template-backend-package/v/unstable)](https://packagist.org/packages/nguyenanhung/template-backend-package) [![License](http://poser.pugx.org/nguyenanhung/template-backend-package/license)](https://packagist.org/packages/nguyenanhung/template-backend-package) [![PHP Version Require](http://poser.pugx.org/nguyenanhung/template-backend-package/require/php)](https://packagist.org/packages/nguyenanhung/template-backend-package)

# Template start Backend API Package

Template for repository Backend API - Basic, Simple and Lightweight

## Use this Template

First, you can `Use this template` for new project: [Use this template](https://github.com/nguyenanhung/template-backend-package/generate)

Second, clone your project to your to path in your machine

Finally, your edit file `composer.json` in root folder of project

```json
{
    "type": "library",
    "name": "nguyenanhung/template-backend-package",
    "description": "Template for repository Backend API  - Basic, Simple and Lightweight",
    "keywords": [
        "template",
        "backend",
        "api",
        "php"
    ],
    "homepage": "https://github.com/nguyenanhung/template-backend-package",
    "license": "MIT",
    "minimum-stability": "stable",
    "authors": [
        {
            "name": "Nguyen An Hung",
            "email": "dev@nguyenanhung.com",
            "homepage": "https://nguyenanhung.com",
            "role": "Developer"
        }
    ],
    "require": {
        "php": ">=7.1.3"
    },
    "autoload": {
        "psr-4": {
            "nguyenanhung\\Backend\\Your_Project\\": "src/"
        },
        "files": [
            "helpers/helpers.php"
        ]
    }
}

```

Replace name space `REPLACE_FOR_YOUR` to Project Backend space, example: `Google`. After change namespace, project namespace same `"nguyenanhung\\Backend\\Google\\": "src/"`

Finished, your can writing new awesome helper and library now time.

## Contact & Support

If any question & request, please contact following information

| Name        | Email                | Skype            | Facebook      |
|-------------|----------------------|------------------|---------------|
| Hung Nguyen | dev@nguyenanhung.com | nguyenanhung5891 | @nguyenanhung |

From Vietnam with Love <3