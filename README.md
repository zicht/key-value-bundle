# `zicht/key-value-bundle`
A bundle that stores pre-specified key-value pairs.

# How to
Define a `KeysDefinerInterface` or extend `AbstractKeyDefiner` service in your own bundle and tag 
this service with `zicht_bundle_key_value.keys_definer`

```
class FooBundleKeyDefiner extends AbstractKeyDefiner
{
    public function getPredefinedKeys()
    {
        return [
            PredefinedKey::createKey(
                name: 'zicht.foo_bundle.my_predefined_key',
                default:  $this->getDefaultValue('zicht.foo_bundle.my_predefined_key', false),
                pretty_name: 'My predefined key',
                form_type: 'checkbox',
                form_options: ['required' => false]
            ),
            PredefinedKey::createKey(
                'zicht.foo_bundle.display_almost_soldout_threshold',
                100,
                'Set the threshold-amount for showing "almost soldout!" on various places in the website',
                'number'
            )
        ];
    }
}
```

Every defined key has a unique key and a default value.  When the user
wants to change the associated value, the custom value is stored in the
database as a json_data field.

To distinguish keys between bundles it's recommended to use the following configuration: `vendor.bundle_name.purpose_of_this_key`

Every defined key has a form type and form options, allowing simple
input, such as form type `'text'`, or complicated inputs, such as form
type `'file'` with options `['data_class' => null']`, where the latter
will ask for a file upload.

Make sure the directory `web/media/key_value_storage` exists and is writable.

# Service
If you want to use the key value storage at any place in your code. A storage service is available. Registred with the name `zicht_bundle_key_value.key_value_storage_manager`.

# Twig
There is also a twig extension available.
`getFromKeyValueStorage` method used with the `key` parameter returns the `value`.

# Symfony 
Add the `new Zicht\Bundle\KeyValueBundle\ZichtKeyValueBundle(),` to your `AppKernel` `registerBundles`.

# Sonata
To use the admin `zicht_bundle_key_value.admin.key_value_admin` use this in your `sonata_admin` config to show the admin for your key's.

To change a value in one of your keys you have to add. After you have added the value it becomes editable.

# TODO
* Make a list of questions that should be asked (and have "yes" as an answer) before deciding to define a key. Questions could be: is this value modified often? Do we want to give access to the client to this value and/or modifications? Are there alternatives? Can/will this value be used in a dependecyinjection pattern?
This will prevent creating keys that are not suited to be in the KeyValueStorage.

* Build a bridge to decouple Symfony and Sonata.

# Maintainer
- Erik Trapman <erik@zicht.nl>
