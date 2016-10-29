Laravel Performance
==========

* 计算代码块的执行时长
* 打印代码块的sql

Usage
-----

* 计算代码块的执行时长

```php
pf()->auto('search1');
#代码块
pf()->auto('search1');
var_dump(pf()->getBenchmarkTime());

##### or #####

pf()->start('search2');
#代码块
pf()->end('search2');
var_dump(pf()->getBenchmarkTime());
```

* 打印代码块的sql

```php
pf()->startRecordQueries();
#sql查询代码
$sql = pf()->getSql();
var_dump($sql);
```
