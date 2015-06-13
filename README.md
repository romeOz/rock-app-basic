Basic Application Template for [Rock Framework](https://github.com/romeOz/rock)
=================

[![Build Status](https://travis-ci.org/romeOz/rock-app-basic.svg?branch=master)](https://travis-ci.org/romeOz/rock-app-basic)
[![HHVM Status](http://hhvm.h4cc.de/badge/romeoz/rock-app-basic.svg)](http://hhvm.h4cc.de/package/romeoz/rock-app-basic)
[![Coverage Status](https://coveralls.io/repos/romeOz/rock-app-basic/badge.svg?branch=master)](https://coveralls.io/r/romeOz/rock-app-basic?branch=master)
[![License](https://poser.pugx.org/romeOz/rock-app-basic/license.svg)](https://packagist.org/packages/romeOz/rock-app-basic)

Installation
-------------------

From the Command Line:

`composer require romeoz/rock-app-basic:*`

In your composer.json:

```json
{
    "require": {
        "romeoz/rock-app-basic": "*"
    }
}
```

If you want to create tables `Users` and `RBAC`, then run `/path/to/apps/common/migrations/bootstrap.php`.


Demo & Tests (one of two ways)
-------------------

####1. Docker + Ansible

 * [Install Docker](https://docs.docker.com/installation/) or [askubuntu](http://askubuntu.com/a/473720)
 * `docker run -d -p 8080:80 romeoz/rock-app-basic`
 * Open demo [http://localhost:8080/](http://localhost:8080/)
 
####2. VirtualBox + Vagrant + Ansible

 * `composer create-project --prefer-dist romeoz/rock-app-basic:*`
 * [Install VirtualBox](https://www.virtualbox.org/wiki/Downloads)
 * [Install Vagrant](https://www.vagrantup.com/downloads), and additional Vagrant plugins `vagrant plugin install vagrant-hostsupdater vagrant-vbguest vagrant-cachier`
 * [Install Ansible](http://docs.ansible.com/intro_installation.html#latest-releases-via-apt-ubuntu)
 * `vagrant up`
 * Open demo [http://rock-basic/](http:/rock-basic/) or [http://192.168.55.55/](http://192.168.55.55/)

> Work/editing the project can be done via ssh:

```bash
vagrant ssh
cd /var/www/rock-basic
```

####Out of the box:

 * Ubuntu 14.04 64 bit

> If you need to use 32 bit of Ubuntu, then uncomment `config.vm.box_url` the appropriate version in the file `/path/to/Vagrantfile`.

 * Nginx 1.6
 * PHP-FPM 5.6
 * MySQL 5.6
 * Composer

Requirements
-------------------
 * **PHP 5.4+**
 * **MySQL 5.5+**
 
Configure server
-------------------

For a single entry point.

####Apache

Security via "white list":

```
RewriteCond %{REQUEST_URI} ^\/(?!index\.php|robots\.txt|500\.html|favicon\.ico||assets\b\/.+\.(?:js|ts|css|ico|xml|swf|flv|pdf|xls|htc|gif|jpg|png|jpeg)$).*$ [NC]
RewriteRule ^.*$ index.php [L]
```

####Nginx

Security via "white list":

```
location ~ ^\/(?!index\.php|robots\.txt|favicon\.ico|500\.html|assets\b\/.+\.(?:js|ts|css|ico|xml|swf|flv|pdf|xls|htc|gif|jpg|png|jpeg)$).*$
{
    rewrite ^.*$ /index.php;
}
```

or [**optimal version**](https://github.com/romeOz/rock-app-basic/blob/master/provisioning/roles/nginx/templates/site.conf) ([recommended](https://events.yandex.ru/lib/talks/2392/) Igor Sysoev) 

License
-------------------

Basic Application Template for [Rock Framework](https://github.com/romeOz/rock) is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).