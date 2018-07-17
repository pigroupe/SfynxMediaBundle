#Configuration Reference

All available configuration options are listed below with their default values.

``` yaml
#
# SfynxMediaBundle configuration
#
sfynx_media:
    mapping:
        provider: orm
        media_class: Sfynx\MediaBundle\Layers\Domain\Entity\Media
        media_entitymanager_command: doctrine.orm.entity_manager
        media_entitymanager_query: doctrine.orm.entity_manager
        mediatheque_class: Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque
        mediatheque_entitymanager_command: doctrine.orm.entity_manager
        mediatheque_entitymanager_query: doctrine.orm.entity_manager
    storage:
        provider: sfynx.media.storage_provider.api_media
    cache_dir:
        media: '%kernel.cache_dir%/Media/'
    asynchrone_format_creation_options:
        parallel_limit: 7 # number of requests that can be sent in parallel
        curlopt_timeout_ms: 300 # maximum number of milliseconds allowed for cURL functions to execute
        timeout_wait_response: 0.05 # Time, in seconds, to wait for a response.
    formats:
        reference: {resize: 0}
        slider-small: { resize: 1, width: 75, maxAge: 31536000}
        slider-big: { resize: 1, width: 410, maxAge: 31536000}
        galery-small: { resize: 1, width: 300, maxAge: 31536000}
        galery-big: { resize: 1, width: 1000, maxAge: 31536000}
    quality:
        default: 95
    crop:
        formats:
            0:
                prefix: default_mosaique
                legend: Mosaique
                width: 380
                height: 380
                ratio: 1
                quality: 70
            1:
                prefix: default_big
                legend: Big
                width: 760
                height: 380
                ratio: 2
                quality: 70
```