### 控制台

#### 命令进入根目录执行 app/console

```
  - - - - - -         - - - - -      - - - - - -     \ \         \ \       - - - - - -
 / - - - - - /     \ / / - - -  /  / - - - - -  \     \ \         \ \    \ \- - - - - -\
\ \                 \ \             \ \          \ \   \ \         \ \    \ \           \ \
 \ \        - - -    \ \             \ \          \ \   \ \         \ \    \ \- - - - - / /
  \ \      / - - -/   \ \             \ \          \ \   \ \         \ \    \ \ - - - - -
   \ \        \ \      \ \             \ \          \ \   \ \         \ \    \ \
    \ \ - - -  \ \      \ \             \ \ - - - - - /    \ \ - - - - \      \ \
     \ - -- -  \ /       \ \             \ - - - - -/       \ - - -  - - /     \ \

 使用帮助: 
 Usage: app/console [options] [args...] 

 generate:service name                               生成一个自定义service
 generate:controller name|groupname:name             生成一个自定义controller(默认存放在src/Web,如果想要指定分组 groupname:name)
 sql:generate                                        生成一个sql执行模板(存放于app/sql)
 sql:migrate                                         执行sql更新
 sql:rollback version                                执行sql回滚到指定版本
 log.clear                                           清除日志

```

#### 自定义控制台 {#自定义控制台}

##### 配置文件config/app.php {#配置文件configappphp}

```
//扩展console命令行控制台
'consoleCommands' => [
    'log:clear' => [
        'command' => 'src\Web\Command\LogClearCommand', //执行的类
        'help' => '清除日志', //提示
    ],
],
```



