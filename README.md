docker-php
======

A class for communicating with the Docker Remote API. http://docker.io

I'm working on another project where I am using this class and will keep this up to date as I work on that project.

As of right now, this class is largely un-tested.

Example:
```php
<?php
include 'docker-client.php';
$client = new DockerClient();
$images = $client->images();
?>
```
