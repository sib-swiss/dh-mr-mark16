# Contribute

This project is a part of the main [VRE](https://gitlab.sib.swiss/mark16-vre-group) project:

* VRE: https://gitlab.isb-sib.ch/mark16-vre-group/vre
* Manuscript Room (this project): https://gitlab.sib.swiss/mark16-vre-group/manuscript
* eTalk: https://gitlab.sib.swiss/mark16-vre-group/etalk

You can find more details [here](../README.md).

<!-- [TOC] -->
<!-- {:toc} -->
<!-- The winning format is below -->
[[_TOC_]]

## Documentation

Before being able to do any changes in the project, you must read the following documentation:

* __Architecture__
  * [RMR](https://www.peej.co.uk/articles/rmr-architecture.html)
    * References:
      * https://fatfreeframework.com/3.7/routing-engine#ReST:RepresentationalStateTransfer
      * https://herbertograca.com/2018/08/31/resource-method-representation/
      * https://softwareengineering.stackexchange.com/questions/146543/separation-of-concerns-in-an-rmr-framework
      * https://dev.to/mattsparks/building-a-php-framework-part-3---time-for-action-9pl
* __Backend__
  * [IIIF](http://iiif.io/)
    * v2.1
      * https://iiif.io/api/image/2.1/
      * https://iiif.io/api/presentation/2.1/
    * v3.0
      * https://iiif.io/api/image/3.0/
      * https://iiif.io/api/presentation/3.0/
  * [F3](https://fatfreeframework.com/3.7/home)
  * [Fomantic-UI]()
* __Frontend__
  * [Bootstrap 4]()
  * [Mirador](https://projectmirador.org/)
    * v2.x
      * https://github.com/ProjectMirador/mirador-2-wiki/wiki
    * v3.x
      * https://github.com/projectmirador/mirador/wiki

## Commits

All commit messages __should__ follow the [gitmoji](https://gitmoji.carloscuesta.me/) specification.


__All changes must be commited into the dev branch__ before merging them into the __master__ branch.



## Deployment

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

> The `php-curl` extension might be already installed but it is not required by the code. Also, the micro-framework behind the project ([F3](https://fatfreeframework.com/3.7/home)) will rely on `TCP` sockets when `curl` is not installed.

### Local

You can change the listened address and port by changing these variables in the script:

1. `LISTEN_ADDRESS`
2. `LISTEN_PORT`

> Please, don't change the other config variables if not needed.

```bash
# Move to the project folder
cd manuscript

# Start the local server
./start-local-server.sh
```

The application is reachable only from __HTTP__ when ran locally:

* http://localhost:8000/htdocs

You can change the listened address and port by changing these variables in the script:

1. `LISTEN_ADDRESS`
2. `LISTEN_PORT`

> Please, don't change the other config variables if not needed.
>
> Don't use this one: http://localhost:8000, __it will not work.__

### Dev

```bash
# Move to the project folder
cd manuscript

# Edit the deployment script
# 1. Comment the line 3
# 2. Set READY=true
nano deploy-on-dev.sh

# Run the deployment script
./deploy-on-dev.sh
```

### Prod

```bash
# Move to the project folder
cd manuscript

# Edit the deployment script
# 1. Comment the line 3
# 2. Set READY=true
nano deploy-on-prod.sh

# Run the deployment script
./deploy-on-prod.sh
```

> This document is a draft, it will be improved later.
