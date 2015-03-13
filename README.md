Basic Application Template for [Rock Framework](https://github.com/romeOz/rock)
=================

[![Build Status](https://travis-ci.org/romeOz/rock-app-basic.svg?branch=master)](https://travis-ci.org/romeOz/rock-app-basic)
[![HHVM Status](http://hhvm.h4cc.de/badge/romeoz/rock-app-basic.svg)](http://hhvm.h4cc.de/package/romeoz/rock-app-basic)
[![Coverage Status](https://coveralls.io/repos/romeOz/rock-app-basic/badge.svg?branch=master)](https://coveralls.io/r/romeOz/rock-app-basic?branch=master)
[![License](https://poser.pugx.org/romeOz/rock-app-basic/license.svg)](https://packagist.org/packages/romeOz/rock-app-basic)

Installation
-------------------

From the Command Line:

`composer require romeoz/rock-app-basic:*@dev`

In your composer.json:

```json
{
    "require": {
        "romeoz/rock-app-basic": "*@dev"
    }
}
```

Demo & Tests (one of two ways)
-------------------

####1. Docker + Ansible

 * [Install Docker](https://docs.docker.com/installation/) or [askubuntu](http://askubuntu.com/a/473720)
 * `docker run -d -p 8080:80 romeoz/rock-app-basic`
 * Open demo [http://localhost:8080/](http://localhost:8080/)
 
####2. Vagrant + Ansible

 * `composer create-project --prefer-dist romeoz/rock-app-basic:*@dev`
 * [Install Vagrant](https://www.vagrantup.com/downloads), and additional Vagrant plugins `vagrant plugin install vagrant-hostsupdater vagrant-vbguest vagrant-cachier`
 * `vagrant up`
 * Open demo [http://rock-basic/](http:/rock-basic/) or [http://192.168.33.40/](http://192.168.33.40/)

> Work/editing the project can be done via ssh:

```bash
vagrant ssh
cd /var/www/rock-basic
```

Requirements
-------------------
 * **PHP 5.4+**

License
-------------------

Basic Application Template for [Rock Framework](https://github.com/romeOz/rock) is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).