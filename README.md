#PHP Version >= 5.1.0
## Thorns API
### 1.模版解析
#### arr标签解析
    <arr name='' value='' key=''></arr>
    foreach($array $k => $v)
    name == array数组名称 ; value为$v别名; key为数组的键值(此属性为可选)

#### if标签解析
condition 条件属性
    
    <if condition="false">
        语句1
    <elseif condition="false">
        语句2
    <else />
        语句3
    </if>

#### Include标签解析
    <include file="filename" /> 引入文件 '@'代表当前应用目录

    __ROOT__     整体项目根目录
    __APP__      当前应用根目录
    __PUBLIC__   当前应用公共目录
    __URL__      当前应用控制器 '
    __TR__       当前应用入口
模版中禁止使用`<?php?>`或者`<??>` 便签

### 2. 控制器模块
* assign function: 数据替换函数
* display function： 模版渲染函数