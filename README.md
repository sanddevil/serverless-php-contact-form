# Contact Form using serverless-php

# Usage
## Prerequisites
* [Serverless](https://serverless.com/)
* [Node](https://nodejs.org)
* [Composer](https://getcomposer.org/)
* [Git LFS](https://git-lfs.github.com/)

Install this project:
```
serverless install --url https://github.com/sanddevil/serverless-php-contact-form.git
```

## Deploying to AWS
```
composer install -o --no-dev
serverless deploy
```

## Changes to serverless.yml
You will need to add your own email addresses to this file. You will easily see the placeholders.

## Webpage Blog
This is blogged at http://badzilla.co.uk/drupal-8-static-site-aws-api-gateway-lambda-and-ses-form-processing

The blog is a sequence of blogs on turning a Drupal 8 site to a static site http://badzilla.co.uk/Drupal8-Static

There are a sequence of blogs on using PHP and Serverless together at http://badzilla.co.uk/real-world-php-lambda-app


### Thanks
Big thanks to Andy Raines. For documentation on his serverless-php repo, point a browser to https://github.com/araines/serverless-php

