# zicht/key-value-bundle
A bundle that stores pre-specified key-value pairs.

# How to
Define a `KeysDefinerInterface` service in your own bundle and tag this service with `zicht_bundle_key_value.keys_definer`

Every defined key has a unique key and a default value.  When the user
wants to change the associated value, the custom value is stored in the
database as a json_data field.

Every defined key has a form type and form options, allowing simple
input, such as form type `'text'`, or complicated inputs, such as form
type `'file'` with options `['data_class' => null']`, where the latter
will ask for a file upload.

Make sure the directory `web/media/key_value_storage` exists and is writable.

# Maintainer(s)
- Erik Trapman <erik@zicht.nl>
