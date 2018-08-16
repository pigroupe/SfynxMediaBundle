# Reference Configuration

## Summary

* [A complete configuration example](#a-complete-configuration-example)
* [Configure your mappings entity manager](#configure-your-mappings-entity-manager)
* [Configure the default quality value when creating image media](#configure-the-default-quality-value-when-creating-image-media)
* [Configure the default jwt token parameters when creating](#configure-the-default-jwt-token-parameters-when-creating)

#### Configure your mappings entity manager

```yml
# app/config/config.yml

sfynx_media:
     mapping:
         entities:
             media:
                 class: Sfynx\MediaBundle\Layers\Domain\Entity\Media
                 provider_command: 'orm'
                 provider_query: 'orm'
                 em_command: doctrine.orm.entity_manager
                 em_query: doctrine.orm.entity_manager
                 repository_command: Sfynx\MediaBundle\Layers\Infrastructure\Persistence\Adapter\Command\Orm\MediathequeRepository
                 repository_query: Sfynx\MediaBundle\Layers\Infrastructure\Persistence\Adapter\Query\Orm\MediathequeRepository
             mediatheque:
                 class: Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque
                 provider_command: 'orm'
                 provider_query: 'orm'
                 em_command: doctrine.orm.entity_manager
                 em_query: doctrine.orm.entity_manager #doctrine.orm.apimedia_entity_manager
                 repository_command: Sfynx\MediaBundle\Layers\Infrastructure\Persistence\Adapter\Command\Orm\MediaRepository
                 repository_query: Sfynx\MediaBundle\Layers\Infrastructure\Persistence\Adapter\Query\Orm\MediaRepository
```

#### Configure the default quality value when creating image media

```yml
# app/config/config.yml

sfynx_media:
    media:
        quality: 115
```

#### Configure the default jwt token parameters when creating

```yml
# app/config/config.yml

sfynx_media:
    media:
        token: { start: 0, expire: 3600, unique: true, ipRange: {} }
```

#### A complete configuration example

All available configuration options are listed below with their default values.

```yml
#
# SfynxMediaBundle configuration
#
sfynx_media:
     mapping:
         entities:
             media:
                 class: Sfynx\MediaBundle\Layers\Domain\Entity\Media
                 provider_command: 'orm'
                 provider_query: 'orm'
                 em_command: doctrine.orm.entity_manager
                 em_query: doctrine.orm.entity_manager
                 repository_command: Sfynx\MediaBundle\Layers\Infrastructure\Persistence\Adapter\Command\Orm\MediathequeRepository
                 repository_query: Sfynx\MediaBundle\Layers\Infrastructure\Persistence\Adapter\Query\Orm\MediathequeRepository
             mediatheque:
                 class: Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque
                 provider_command: 'orm'
                 provider_query: 'orm'
                 em_command: doctrine.orm.entity_manager
                 em_query: doctrine.orm.entity_manager #doctrine.orm.apimedia_entity_manager
                 repository_command: Sfynx\MediaBundle\Layers\Infrastructure\Persistence\Adapter\Command\Orm\MediaRepository
                 repository_query: Sfynx\MediaBundle\Layers\Infrastructure\Persistence\Adapter\Query\Orm\MediaRepository
     storage:
         provider: sfynx.media.storage_provider.api_media
     cache_dir:
         media: "%sfynx_cache_dir%/Media/"
     asynchrone_format_creation_options:
         parallel_limit: 7 # number of requests that can be sent in parallel
         curlopt_timeout_ms: 300 # maximum number of milliseconds allowed for cURL functions to execute
         timeout_wait_response: 0.05 # Time, in seconds, to wait for a response.
     formats:
         reference: {resize: 0}
         slider_small: { resize: 1, width: 75, maxAge: 31536000}
         slider_big: { resize: 1, width: 410, maxAge: 31536000}
         galery_small: { resize: 1, width: 300, maxAge: 31536000}
         galery_big: { resize: 1, width: 1000, maxAge: 31536000}
     media:
         quality: 115
         token: { start: 0, expire: 3600, unique: true, ipRange: {} }
     crop:
         formats:
             0:
                 prefix: slider_big
                 legend: Litle
                 width: 75
                 height: 380
                 ratio: 1
                 quality: 95
             1:
                 prefix: slider_big
                 legend: Mosaique
                 width: 150
                 height: 380
                 ratio: 1
                 quality: 95
             2:
                 prefix: slider_big
                 legend: Big
                 width: 300
                 height: 380
                 ratio: 2
                 quality: 95
```