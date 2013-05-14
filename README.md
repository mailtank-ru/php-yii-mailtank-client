### Yii framework (1.1.x) PHP client extension for mailtank.ru

Подключение:

Склонировать в `ext.yii-mailtank-client.MailtankClient`

main.php
```php
'preload' => 'mailtank',
'components' => array(
...
        'mailtank' => array(
            'class' => 'ext.yii-mailtank-client.MailtankClient',
            'host' => 'api.mailtank.ru',
            'token' => 'your-api-key',
        ),
...
)
```

Простой пример использования:
```php
$subscriber = new MailtankSubscriber();
$subscriber->email = 'first@example.com';
// При обращении к api возможно использование как id, так и external_id
$subscriber->external_id = $myInternalId;
$subscriber->save();

$subscriber2 = new MailtankSubscriber();
$subscriber2->email = 'second@example.com';
$subscriber2->tags = array('my_tag');
$subscriber2->save();

$layout = new MailtankLayout();
$layout->setAttributes(
  array(
    'external_id' => $my_prefered_id,
    'name' => 'My awesome template',
    'markup' => 'Hello from {{app_name}}'
));
$layout->save();

$mailing = new MailtankMailing();
$mailing->setAttributes(array(
    'title' => 'Hello!',
    'layout_id' => $layout->id,
    'context' => array('app_name' => Yii::app()->name),
    'target' => array(
        'tags' => array('my_tag'),
        'subscribers' => array($subscriber2),
    ),
));
$mailing->save();

while(!in_array($mailing->status, array('SUCCEEDED', 'FAILED'))) {
  sleep(5);
  $mailing->refresh();
}

echo "Hey, your mailing is {$mailing->status}!"; 

```
