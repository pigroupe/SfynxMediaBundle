# How to use Form type

## Summary

* [Load the media form resource used by media form type](#load-the-media-form-resource-used-by-media-form-type)
* [OneToOne Relation with a media](#onetoone-relation-with-a-media)
* [ManyToMany Relation with a media](#manytomany-relation-with-a-media)
* [Without media entity relation](#without-media-entity-relation)


### Load the media form resource used by media form type

```yml
# app/config/config.yml
twig:
    ...
    form:
        resources:
            - 'SfynxMediaBundle:Form:fields.html.twig'
```

### OneToOne Relation with a media

In your entity:

```php
/**
 * @var Media
 *
 * @ORM\OneToOne(targetEntity="Sfynx\MediaBundle\SfynxMediaBundle\Entity\Media", cascade={"all"})
 * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
 */
private $media;
```

In the entity form type:

```php
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ...
        ->add('media', 'related_to_one_media', array(
            'data' => $builder->getData()->getMedia()
        ))
        ...
    ;
}
```

### ManyToMany Relation with a media

In your entity:

```php
/**
 * @var array<Media>
 *
 * @ORM\ManyToMany(targetEntity="Sfynx\MediaBundle\SfynxMediaBundle\Entity\Media", cascade={"all"})
 * @ORM\JoinTable(name="my_entity_media",
 *     joinColumns={@ORM\JoinColumn(name="my_entity_id", referencedColumnName="id", onDelete="cascade")},
 *     inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="cascade")}
 * )
 */
private $images;
```

Additional information:

If you want a ManyToOne behavior, just add a UNIQUE constraint to the media key.
Replace the inverseJoinColumns line with:

```php
inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id", unique=true, onDelete="cascade")}
```

In the entity form type:

```php
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ...
        ->add('images', 'related_to_many_media')
        ...
    ;
}
```

### Without media entity relation

In your entity:

```php
/**
 * @var string
 *
 * @ORM\Column(name="image", type="text")
 */
private $image;
```

In this entity form type:

```php
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ...
        ->add('image', 'direct_link_media')
        ...
    ;
}
```