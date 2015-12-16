#!/usr/bin/env php
<?php
require_once 'vendor/autoload.php';
// 初期セットアップ、環境変数も見る
$cmd = new Commando\Command();

$cmd->option('mysql_base')
    ->describedAs('インストール済みMysql base dir (/usr や /usr/local/Cellar/mysql/5.6.27 (brew)).')
    ->default('/usr/local/Cellar/mysql/5.6.27');

$cmd->option('port')
    ->describedAs('生成する設定ファイルに記述するmysql の受付ポート')
    ->default('3306');

$cmd->option('datadir')
    ->describedAs('新しく作成するmysql dataディレクトリの設置先')
    ->default(realpath(getcwd())); // カレントディレクトリ

// 処理していくぞ
$dir = $cmd['datadir'];

// datadirが本当に存在して空かチェックする
if(!file_exists($dir) || filetype($dir)!=='dir'){
    echo "インストール先ディレクトリがみつかりません";
    exit(1);
}

$file_list = glob($dir.'/*');
if(count($file_list)>0){
    echo "インストール先ディレクトリが空ではないです";
    exit(1);
}

// mysqlのBasedirがあっているか確認する
// Could not find my-default.cnf
if(!file_exists("{$cmd['mysql_base']}/support-files/my-default.cnf")){
    echo "mysql base dirがみつかりません(/support-files/my-default.cnfがみつかりません)";
    exit(1);
}

chdir($dir);

$mysql_install_db = $cmd['mysql_base']."/bin/mysql_install_db --datadir={$dir}/mysql_data/ --basedir=".$cmd['mysql_base'];

//本当につくっていいか聞く
echo "以下の内容で作成しますか？
（変更したい場合には、 --help を参照してください）
--
作成されるmysqlのdata_dir: {$dir}/mysql_data
起動停止スクリプト生成先: {$dir}
接続受付ポート: {$cmd['port']}
利用するmysqlのbase dir: {$cmd['mysql_base']}
--
以下コマンドが実行され、同時にスクリプトファイルが生成されます。
$ {$mysql_install_db}

y/n: ";
if(trim(fgets(STDIN))!=='y'){
    echo "停止しました";
    exit(0);
}


//実際に操作していくぞ
echo "============\n";
echo `$mysql_install_db`;
echo "============\n";

file_put_contents('start_mysql.sh', "#!/bin/sh
mysqld_safe --defaults-file={$dir}/mysql_data/my.cnf &");
chmod('start_mysql.sh', 0755);

file_put_contents('stop_mysql.sh', "#!/bin/sh
mysqladmin shutdown -u root --socket={$dir}/mysql_data/socket");
chmod('stop_mysql.sh', 0755);

file_put_contents('mysql_data/my.cnf', "[mysqld]
basedir = {$cmd['mysql_base']}
datadir = {$dir}/mysql_data
socket = {$dir}/mysql_data/socket
port = {$cmd['port']}
bind-address = 127.0.0.1");

echo "完了しました！\n\n起動には ./start_mysql.sh 停止には ./stop_mysql.sh を実行してください
うまく起動しない場合には {$dir}/mysql_data/マシン名.err をみてください\n";
