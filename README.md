# mysql_data_setup.php

Mysqlのdata dirをシュッと好きな所に作成し、そのdata dirをつかってMysqlを起動するためのスクリプトも生成します。

生成されたdata dirの中にデータ、設定ファイル、sockなどが納まります。ファイルを散らばらさず、既存をよごさず、開発時にまっさらなMysqldをシュッと用意するために便利です。

# require

- php>=5.6
- mysql>=5.6

開発・テストしているのが上の環境であり、いくらか過去のバージョンでも動くと思います。

# install

mysqlはBrewなどで事前に入れて下さい。

```
$ git clone this_repo
$ cd this_repo
$ composer install
```

# usage

空のディレクトリを用意し、以下のように生成、起動します。

```
# 作成
$ mkdir project_mysql
$ cd project_mysql
$ /path/to/mysql_data_setup.php

# 開始
$ start_mysql.sh

# 停止
$ stop_mysql.sh

# 削除（初期化は削除＞作成してください）
$ stop_mysql.sh
$ rmdir -r project_mysql
```

socketファイルは `mysql_data/socket` に作成されます。

初期パスワードなどは設定しません、rootと空パスです。必要に応じて変更してください。

接続ポート番号はデフォルト3306ですが、変更する場合には生成された `mysql_data/my.cnf` を修正するか、 `--help` オプションをみてください。

# LICENSE

MIT
