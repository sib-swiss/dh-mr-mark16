# APIs

This folder contains the APIs used by the project.

## IIIF

This API is used to generate the content required by [Mirador](https://github.com/ProjectMirador/mirador/) viewer.

Supported specifications:

* [Presentation API](https://iiif.io/technical-details/#presentation-api)
  * Version: [2.1](https://iiif.io/api/presentation/2.1/)
  * Version: [3.0](https://iiif.io/api/presentation/3.0/)
* [Image API](https://iiif.io/technical-details/#image-api)
  * Version: [2.1](https://iiif.io/api/image/2.1/)
  * Version: [3.0](https://iiif.io/api/image/3.0/)

Supported versions:

* 2.1 (_Almost complete_)
* 3.0 (_Incomplete_)

> This one cover the complete scope of the IIIF Specifications.

It has been written from scratch by following one by one each specification parts.

Other existing APIs:

1. https://github.com/yale-web-technologies/IIIF-Manifest-Generator
   * __*Only cover the Presentation API*__
2. https://github.com/conlect/image-iiif
   * __*Only cover the Image API*__

> None of them are covering the complete scope of the IIIF Specifications.

## MR

This API is used to parse XML documents from [Nakala](https://www.nakala.fr/).

It is for now splitted in three parts:

1. The `REST` API written in `PHP`.
2. The `XML`/`RDF` parser written in `PHP`.
3. The Text viewer written in `JS`.