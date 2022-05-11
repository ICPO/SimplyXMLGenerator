## Базовая инициализация
```php
$xml = new xml();
$xml->createDocument();
$xml->wrap('properties');

$xml->contentWrapBegin('property');
$xml->addNode('image','http://site.ru');
$xml->contentWrapEnd();

$xml->saveDocument();
```
Результат выполнения кода (XML файл):

```xml
<?xml version="1.0" encoding="UTF-8"?>
<properties>
  <property>
    <image>http://site.ru</image>
  </property>
</properties>
```

## Описание методов
1) `$xml->createDocument()` - Начинает создание документа. Принимает параметры __версия__, __кодировка__, __форм. вывод__.
2) `$xml->wrap('properties')` - Root обертка. Если не указать, то по умолчанию будет использоваться Root узел __objects__.
3) `$xml->contentWrapBegin('property')` - Обертка для потомков, внутрь которой будут добавляться узлы. Модуль запоминает последнее переданное имя и будет использовать его до тех пор, пока не будет передано новое.
4) `$xml->addNode('image','http://site.ru')` - Пример базового добавления информационного узла. Более подробно в примерах ниже.
5) `$xml->contentWrapEnd()` - Завершает формирование потомка и вложенных в него узлов.
6) `$xml->saveDocument()` - Сохраняет документ. В качестве параметра можно передать путь + имя файла. Например, __path_to_directory/example.xml__. Если параметр не передан, файл будет сохранен в ту же дирректорию, где расположен файл модуль.

### Дополнительные методы
1) `validateXML()` - Валидирует XML разметку.
2) `showXML($path)` - Выводит XML файл на экран. В качестве параметра передается полный путь до XML файла.
## Расширенные примеры
1) Пример формирования CDATA контента
```php
$xml = new xml();
$xml->createDocument();
$xml->wrap('properties');

$xml->contentWrapBegin('property');
$xml->addNode('images','http://site.ru',false,true);
$xml->contentWrapEnd();

$xml->saveDocument();
```
Результат выполнения кода
```xml
<?xml version="1.0" encoding="UTF-8"?>
<properties>
  <property>
    <images><![CDATA[http://site.ru]]></images>
  </property>
</properties>
```
2) Пример обработки массива данных с применением CDATA
```php
$xml = new xml();
$xml->createDocument();
$xml->wrap('properties');

$xml->contentWrapBegin('property');
$xml->addNode('images',['http://site.ru','http://site.ru'],'url',true);
$xml->contentWrapEnd();

$xml->saveDocument();
```
Результат выполнения кода
```xml
<?xml version="1.0" encoding="UTF-8"?>
<properties>
  <property>
    <images>
      <url><![CDATA[http://site.ru]]></url>
      <url><![CDATA[http://site.ru]]></url>
    </images>
  </property>
</properties>
```
3) Присваивание атрибутов узлу `images`
```php
$xml = new xml();
$xml->createDocument();
$xml->wrap('properties');

$xml->contentWrapBegin('property');
$xml->addNode('images',['http://site.ru','http://site.ru'],'url',true,[['mime-type'=>'image/jpeg']]);
$xml->contentWrapEnd();

$xml->saveDocument();
```
Результат выполнения кода
```xml
<?xml version="1.0" encoding="UTF-8"?>
<properties>
  <property>
    <images mime-type="image/jpeg">
      <url><![CDATA[http://site.ru]]></url>
      <url><![CDATA[http://site.ru]]></url>
    </images>
  </property>
</properties>
```
4) Присваивание атрибутов узлам `url`
```php
$xml = new xml();
$xml->createDocument();
$xml->wrap('properties');

$xml->contentWrapBegin('property');
$xml->addNode('images',['http://site.ru','http://site.ru'],'url',true,[['mime-type'=>'image/jpeg']],
    [
        ['param1'=>'value1']
    ]
);
$xml->contentWrapEnd();

$xml->saveDocument();
```
Результат выполнения кода
```xml
<?xml version="1.0" encoding="UTF-8"?>
<properties>
  <property>
    <images mime-type="image/jpeg">
      <url param1="value1"><![CDATA[http://site.ru]]></url>
      <url><![CDATA[http://site.ru]]></url>
    </images>
  </property>
</properties>
```
5) Присваивание атрибутов 1му и 3му узлу `url`
```php
$xml = new xml();
$xml->createDocument();
$xml->wrap('properties');

$xml->contentWrapBegin('property');
$xml->addNode('images',['http://site.ru','http://site.ru','http://site.ru'],'url',false,[['mime-type'=>'image/jpeg']],
    [
        ['param1'=>'value1'],
        [],
        ['param3'=>'value3']
    ]
);
$xml->contentWrapEnd();

$xml->saveDocument();
```
Результат выполнения кода
```xml
<?xml version="1.0" encoding="UTF-8"?>
<properties>
  <property>
    <images mime-type="image/jpeg">
      <url param1="value1">http://site.ru</url>
      <url>http://site.ru</url>
      <url param3="value3">http://site.ru</url>
    </images>
  </property>
</properties>
```
6) Пример с множественными потомками для корневого узла `properties`
```php
$xml = new xml();
$xml->createDocument();
$xml->wrap('properties');

$xml->contentWrapBegin('property');
$xml->addNode('address','anywhere',false,true);
$xml->addNode('houses',['economy','business'],'house',true,false,
[
    ['material'=>'wood','floors'=>2,'bedrooms'=>2, 'bathrooms'=>1], //Параметры для Эко дома
    ['material'=>'concrete','floors'=>2,'bedrooms'=>3,'bathrooms'=>2], //Параметры для Бизнес дома
]);
$xml->addNode('price',400000);
$xml->addNode('images',['http://site.ru/eco_plan.png','http://site.ru/business_plan.jpg'],'image',true,
    [
        ['count-pictures'=>2]
    ],
    [
        ['mime-type'=>'png','alt'=>'Eco house plan'],
        ['mime-type'=>'jpg','alt'=>'Business house plan']
    ]
);
$xml->contentWrapEnd();

$xml->contentWrapBegin('property');
$xml->addNode('address','somewhere far away',false,true);
$xml->addNode('houses',['bungalow'],'house',true,false,
[
    ['material'=>'cloth','floors'=>1,'bedrooms'=>1, 'bathrooms'=>0],
]);
$xml->addNode('price',2000);
$xml->addNode('images',['http://site.ru/eco_plan.png'],'image',true,
    [
        ['count-pictures'=>1]
    ],
    [
        ['mime-type'=>'png','alt'=>'Bungalow house plan'],
    ]
);
$xml->contentWrapEnd();

$xml->saveDocument();
```
Результат выполнения кода
```xml
<?xml version="1.0" encoding="UTF-8"?>
<properties>
  <property>
    <address><![CDATA[anywhere]]></address>
    <houses>
      <house material="wood" floors="2" bedrooms="2" bathrooms="1"><![CDATA[economy]]></house>
      <house material="concrete" floors="2" bedrooms="3" bathrooms="2"><![CDATA[business]]></house>
    </houses>
    <price>400000</price>
    <images count-pictures="2">
      <image mime-type="png" alt="Eco house plan"><![CDATA[http://site.ru/eco_plan.png]]></image>
      <image mime-type="jpg" alt="Business house plan"><![CDATA[http://site.ru/business_plan.jpg]]></image>
    </images>
  </property>
  <property>
    <address><![CDATA[somewhere far away]]></address>
    <houses>
      <house material="cloth" floors="1" bedrooms="1" bathrooms="0"><![CDATA[bungalow]]></house>
    </houses>
    <price>2000</price>
    <images count-pictures="1">
      <image mime-type="png" alt="Bungalow house plan"><![CDATA[http://site.ru/eco_plan.png]]></image>
    </images>
  </property>
</properties>

```

