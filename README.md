# MARK16 MR API

(C) Copyright SIB Swiss Institute of Bioinformatics 2021, available from https://github.com/sib-swiss/pftools3 under GNU GPL v3.0 See LICENSE.

This API is a part of the main SNSF MARK16 [VRE](https://gitlab.sib.swiss/mark16-vre-group) project:

* VRE: https://gitlab.isb-sib.ch/mark16-vre-group/vre
* Manuscript Room (this project): https://gitlab.sib.swiss/mark16-vre-group/manuscript
* eTalk: https://gitlab.sib.swiss/mark16-vre-group/etalk

SNSF MARK16 project: https://mark16.sib.swiss; DH+, SIB Swiss Institute of Bioinformatics, CH, 2020, ISSN 2673-9836

<!-- [TOC] -->
<!-- {:toc} -->
<!-- The winning format is below -->

## Definition and credits

This manuscript viewer makes it possible to view manuscripts along with their transcripts, and in some extent manipulate the images.

The use of an internal image server to make [Mirador](https://projectmirador.org/) being able to work with images served locally is based on a test case available at https://iiif.github.io/training/intro-to-iiif/.

The first implementation of this API has been made by __Jean-Bernard Dugied__, then developed by __Jonathan Barda__ and __Silvano Aldà__.

The API is now maintained by __Jonathan Barda__ and __Silvano Aldà__.

The PI of the five-year SNSF MARK16 project is __Claire Clivaz__ and the team is composed of __Mina Monier__, post-doc, __Elisa Nury__, research scientist, with __Jonathan Barda__ and __Silvano Aldà__, Core-IT software developers. The project is hosted at Digital Humanities +, SIB Swiss Institute of Bioinformatics.

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

OS:

* Distrib: CentOS 8.3
* Installation type: `Server` (__without GUI__)
* Additional packages: `Basic Web Server`


![image](./doc/centos8-server-package-selection.png)
![image](./doc/centos8-server-setup-summary.png)

### SELinux

If `SELinux` is enabled on the server, you will then need to apply the following contexts:

* `httpd_sys_script_exec_t`
  * on folder __`[web-root]/htdocs`__
* `httpd_sys_rw_content_t`
  * on folder __`[web-root]/data`__
* `httpd_log_t`
  * on folder __`[web-root]/data/logs`__
  * on folder __`[web-root]/logs`__

Using the following commands:

```bash
# Set SELinux context
sudo semanage fcontext -a <context> "<target>(/.*)?"

# Apply SELinux context
sudo restorecon -Rv <target>
```

See [here](https://www.serverlab.ca/tutorials/linux/web-servers-linux/configuring-selinux-policies-for-apache-web-servers/) for more details.