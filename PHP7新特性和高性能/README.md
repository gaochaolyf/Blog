##PHP7新特性
* AST 抽象语法树 ->opcode 静态检测
?:三元表达式 比较坑 非结合性
* int64的支持 所有字符串长度64bit
* 统一的变量语法
```php
Foo::$bar['baz']()
Foo::{$bar['baz']}()    PHP5 
(Foo::$bar)['baz']()    PHP7 
```
* 两个新的操作符
    *  <=>  ,用于对比 $a <=> $b 返回值为a>b 为1,a<b 为-1,其余为0
    *  ??   ,$a ?? NULL 等价于 isset($a) ? $a: NULL
* 函数的声明类型以及返回类型的设定 强类型的开关
* exceptions 异常捕获  fatal_error
* 匿名类 匿名函数
* foreach 行为的改进 未定的方法
foreach不再使用数组内部的指针 迭代器不改变比如current不改变内部指针
减少一致性的变更
* 上下文敏感的词法分析
比如foreach关键字 函数 PHP5
除了class都可以 重新变更 PHP7

##PHP7移除的东西
* 有一部分不常用的扩展被移到了PECL 比如MySQL 推荐用mysqli pdo
* HTTP_RAW_POST_DATE移除 用php://input
* 非兼容的上下文 $this未定义 而不是随意分配一个错的 一致性考虑
* 移除了 $o=&new classname() 对象引用赋值
* php.ini移除了 #注释 只能用;注释

##PHP7改变的东西
* 同名参数 function a($b, $b)
直接fatal_error
* String int float bool 不能再被当做类名
* _get_args 获取一个返回的调用参数值 是调用后的值而非当时的值
```php
v++; 
var_dump(func_get_args()[o])
a(1)
1 PHP5
2 PHP7
```

##PHP7性能提升
* 缓存命中率的提升
* Zval 结构发生改变 从24位降低成16位
* 增加类型 boollen类型拆分成 TRUE和FALSE， 引用类型
Zval refcounted头的结构体
* zend_string 头部和值在同一片cpu缓存中 提高缓存命中率
* zend_array 原来是一个双向链表  现在内存一起分配 尽量内连Hashtable 72->56和bucket 72->32都减少了 现在foreach一个数组 对cpu cache友好 提高了缓存命中率
* zend_object
* zend_reference引用类型 内连一个Zval
* 函数调用的优化 a+b 先初始化init_fcall 参数栈
* 快速的参数处理 宏 额外无用的判断在编译期就优化掉
* 非常常用的函数变成了引擎支持的opcode
    *  is_int/string/array => ZEND_TYPE_CHECK
    *  call_user_function(array) => ZEND_INIT_USER_CALL
    *  strlen => ZEND_STRLEN
    *  defined => ZEND+DEFINED
* zend_qsort PHP7使用的是混合的排序方式(选择排序和快速排序) asort 性能带来40%的提升
    *  当数组count小于16时采用选择排序 当大于16时分组 16/16/16 进行选排序 再进行快速排序
    *  快速排序不稳定 但是选择排序也不稳定，所以没看懂鸟哥的这个例子 
```php
array(0=>0,1=>0)
PHP5 array(1=>0,0=>0) //发生交换
PHP7 array(0=>0,1=>0) //不发生交换 不会写内存
```
* 新的内存管理器 New Memory Manager
    *  更少的内存浪费
    *  对cpu cache的友好性
    *  提高了缓存命中率
    *  更快的分配内存
    *  提高5%性能
* zend_opsize从48减少到32
* gcc4.8+ 把最常用的两个结构体放到寄存器里面去
* pgo 以profile为引导的优化 得到一个profiling result 函数调用频率再告诉gcc 重新编译

##优化结果 wordpress profiled
* 大于50% cpu指次减少 72e次 to 23e次
* 5% cpu时间耗费在内存这块
* 12% 时间花费在hash tables上
* 3倍的性能 速度的提升
* 至少30%的内存减少 可以开更多的进程