# zf2-skeleton
ZF2 - Zend Framework 2 - Skeleton application for zend-mvc projects

Introduction
------------
This is a simple, skeleton application using the ZF2 MVC layer and module
systems. This application is meant to be used as a starting place for those
looking to get their feet wet with ZF2.

Installation using Composer
---------------------------

The easiest way to create a new ZF2 project is to use [Composer](https://getcomposer.org/). If you don't have it already installed, then please install as per the [documentation](https://getcomposer.org/doc/00-intro.md).


Create your new ZF2 project:

    composer self-update
    composer install



### Installation using a tarball with a local Composer

If you don't have composer installed globally then another way to create a new ZF2 project is to download the tarball and install it:

1. Download the [tarball](https://github.com/RenzoTejada/zf2-skeleton.git), extract it and then install the dependencies with a locally installed Composer:

        cd my/project/dir
        git clone https://github.com/RenzoTejada/zf2-skeleton.git
    

2. Download composer into your project directory and install the dependencies:

        curl -s https://getcomposer.org/installer | php
        php composer.phar self-update
        php composer.phar install

If you don't have access to curl, then install Composer into your project as per the [documentation](https://getcomposer.org/doc/00-intro.md).

### Vagrant server

This project supports a basic [Vagrant](http://docs.vagrantup.com/v2/getting-started/index.html) configuration with an inline shell provisioner to run the Skeleton Application in a [VirtualBox](https://www.virtualbox.org/wiki/Downloads).

1. Run vagrant up command

    vagrant up

2. Visit [http://zf2.local/](http://zf2.local/) in your browser

Look in [Vagrantfile](Vagrantfile) for configuration details.

### Apache setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:

    <VirtualHost *:80>
        ServerAdmin admin@admin.com
        ServerName zf2.local
        DocumentRoot /var/www/html/public
        <Directory /var/www/html/public>
            Options Indexes FollowSymLinks MultiViews
            AllowOverride all
            Require all granted
        </Directory>
        ErrorLog /zf2.local-error.log
        CustomLog /zf2.local-access.log combined
    </VirtualHost>
