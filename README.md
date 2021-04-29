Install using composer :
```
composer require pierreminiggio/github-action-run-detailer
```

```php
use PierreMiniggio\GithubActionRunDetailer\GithubActionRunDetailer;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$detailer = new GithubActionRunDetailer();
$detail = $detailer->find(
    'pierreminiggio',
    'remotion-test-github-action',
    789704536
);

var_dump($detail);
```
