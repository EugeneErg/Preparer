# Доступные запросы #

## Insert

### Обычная вставка одной строки ###
```php
$data = new Value(['key1' => 'value1', 'key2' => 'value2']);    
$query = new InsertQuery(
    new Table('table'),
    new Returning(
        new TypeCollection([
            'field1' => $data->getString('key1'),
            'field2' => $data->getString('key2'),
        ]),
        $data,
    ),
);

echo (new Compiler())->toString($query, 'mysql');
```
Выведет следующее:
```sql
insert into `table`
set
    `field1` = 'value1',
    `field2` = 'value2'
```
### Обычная вставка нескольких строк ###
```php
$data = new Values(
    new Value(['key1' => 'value11', 'key2' => 'value12']),
    new Value(['key1' => 'value21', 'key2' => 'value22']),
);    
$query = new InsertQuery(
    new Table('table'),
    new Returning(
        new TypeCollection([
            'field1' => $data->getString('key1'),
            'field2' => $data->getString('key2'),
        ]),
        $data,
    ),
);

echo (new Compiler())->toString($query, 'mysql');
```
Выведет следуюее:
```sql
insert into `table`(
    `field1`,
    `field2`
) values (
    'value11',
    'value12'    
), (
    'value21',
    'value22'
)
```
### Вставка из таблицы ###
```php
$data = new Table('table2');    
$query = new InsertQuery(
    new Table('table'),
    new Returning(
        new TypeCollection([
            'field1' => $data->getString('key1'),
            'field2' => $data->getString('key2'),
        ]),
        $data,
    ),
);

echo (new Compiler())->toString($query, 'mysql');
```
Выведет следуюее:
```sql
insert into `table`
from `table2`
set
    `field1` = `table2`.`key1`,
    `field2` = `table2`.`key2`
```
### Вставка из таблицы и значения ###
```php
$data1 = new Table('table2');
$data2 = new Value(['key2' => 'value2']);
$query = new InsertQuery(
    new Table('table'),
    new Returning(
        new TypeCollection([
            'field1' => $data1->getString('key1'),
            'field2' => $data2->getString('key2'),
        ]),
        (new SelectQuery())->from($data1)->from($data2),
    ),
);

echo (new Compiler())->toString($query, 'mysql');
```
Выведет следуюее:
```sql
insert into `table`
from
    `table2`,
     (select 'value2' as `key2`) `data2`
set
    `field1` = `table2`.`key1`,
    `field2` = `data2`.`key2`
```
### Вставка из таблицы и значений ###
```php
$data1 = new Table('table2');
$data2 = new Values(
    new Value(['key2' => 'value12']),
    new Value(['key2' => 'value22']),
);
$query = new InsertQuery(
    new Table('table'),
    new Returning(
        new TypeCollection([
            'field1' => $data1->getString('key1'),
            'field2' => $data2->getString('key2'),
        ]),
        (new SelectQuery())->from($data1)->from($data2),
    ),
);

echo (new Compiler())->toString($query, 'mysql');
```
Выведет следуюее:
```sql
insert into `table`
from
    `table2`,
     (
        select 'value12' as `key2`
        union all select 'value22'        
    ) `data2`
set
    `field1` = `table2`.`key1`,
    `field2` = `data2`.`key2`
```
### Вставка из подзапроса ###
```php
$data1 = new Table('table2');
$data2 = new Values(
    new Value(['key2' => 'value12']),
    new Value(['key2' => 'value22']),
);
$query = new InsertQuery(
    new Table('table'),
    new Returning(
        new TypeCollection([
            'field1' => $data1->getString('key1'),
            'field2' => $data2->getString('key2'),
        ]),
        (new SelectQuery())->from($data1)->from($data2),
    ),
);

echo (new Compiler())->toString($query, 'mysql');
```
Выведет следуюее:
```sql
insert into `table`
from
    `table2`,
     (
        select 'value12' as `key2`
        union all select 'value22'        
    ) `data2`
set
    `field1` = `table2`.`key1`,
    `field2` = `data2`.`key2`
```




## Docker

Для удобного локального запуска через Docker в директории проекта выполнить:

```
docker-compose up -d
```


### Список команд для локального запуска 
- Запуск общих миграций
```shell
php artisan migrate --force
```
- Запуск миграция для конкретного тенанта
```shell
php artisan [tenant] migrate --path=database/migrations/tenants --force
```
- Запуск очереди по отправки транзакций 
```shell
php artisan queue:work redis --queue={billing_send_transaction_queue}
```
