# VRE Manuscript

This API is a part of the main [VRE](https://gitlab.sib.swiss/mark16-vre-group) project:

* VRE: https://gitlab.isb-sib.ch/mark16-vre-group/vre
* Manuscript Room (this project): https://gitlab.sib.swiss/mark16-vre-group/manuscript
* eTalk: https://gitlab.sib.swiss/mark16-vre-group/etalk


<!-- [TOC] -->
<!-- {:toc} -->
<!-- The winning format is below -->
[[_TOC_]]

## Definition and credits

This manuscript viewer makes it possible to view manuscripts along with their transcripts, and in some extent manipulate the images.

This project owes a great deal to __Elisa Nury__'s advices about possible project structure and tools involved.

The use of an internal image server to make [Mirador](https://projectmirador.org/) being able to work with images served locally is based on a test case available at https://iiif.github.io/training/intro-to-iiif/.

The first implementation of this project has been made by __Jean-Bernard Dugied__.

The project is now maintained by __Jonathan Barda__ and __Silvano Aldà__.

## Local setup

This section will explain the required steps to have a complete and working local development environment.

### PHP

The project is built with `PHP` version __7.4__.

Required packages / extensions:

1. `php-cli`
2. `php-fpm`
3. `php-gd`
4. `php-opcache`
5. `php-xml`
6. `php-sqlite3`
7. `php-pdo`
8. `php-mbstring`
9. `composer`

> The `php-curl` extension might be already installed but it is not required by the code. Also, the micro-framework behind the project ([F3](https://fatfreeframework.com/3.7/home)) will rely on `TCP` sockets when `curl` is not installed.

To install everything once the `OS` is ready, simply run the [install](./install.sh) script.

> The current [install](./install.sh) script is __only compatible with Ubuntu based distribs__, it will adapted soon for CentOS.

### Web server

To start the local web server, simply run the [start-local-server.sh](start-local-server.sh) script:

```bash
# Move to the project folder
cd manuscript

# Run the server
./start-local-server.sh
```

### Access to the application

The application is reachable only from __HTTP__ when ran locally:

* http://localhost:8000/htdocs

You can change the listened address and port by changing these variables in the script:

1. `LISTEN_ADDRESS`
2. `LISTEN_PORT`

> Please, don't change the other config variables if not needed.
>
> Don't use this one: http://localhost:8000, __it will not work.__

## Server setup

Hardware:

* CPUs: 4 vCPUs
* RAM: 4 GB
* Disk: 32 GB
* Network: ULZ

OS:

* Distrib: CentOS 8.3
* Installation type: `Server` (__without GUI__)
* Additional packages: `Basic Web Server`


![image](./doc/centos8-server-package-selection.png)
![image](./doc/centos8-server-setup-summary.png)


## TODO

* Finish this document :grin:

> This document is a draft, it will be improved later.
