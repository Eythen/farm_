#!/bin/bash
#备份路径
BACKUP=/home/wwwroot/m.septfarm.com/backup/mysql
#当前时间
DATETIME=$(date +%Y-%m-%d_%H%M%S)
echo "===备份开始==="
echo "备份文件存放于${BACKUP}/$DATETIME.tar.gz"
#数据库地址
#HOST=localhost
#数据库用户名
#DB_USER=weixin
#数据库密码
#DB_PW=cr123678
#创建备份目录
#改成/etc/my.cnf 里面设置
#[mysqldump]
#user=root
#password=641868752

[ ! -d "${BACKUP}/$DATETIME" ] && mkdir -p "${BACKUP}/$DATETIME"

#后台系统数据库
DATABASE=m_septfarm_com
#mysqldump -u${DB_USER} -p${DB_PW} --host=$HOST -q -R --databases $DATABASE | gzip > ${BACKUP}/$DATETIME/$DATABASE.sql.gz
#mysqldump -u${DB_USER} -p${DB_PW} --host=$HOST -q -R --databases $DATABASE | gzip > ${BACKUP}/$DATETIME/$DATABASE.sql.gz
mysqldump -q -R --databases $DATABASE | gzip > ${BACKUP}/$DATETIME/$DATABASE.sql.gz

#压缩成tar.gz包
cd $BACKUP
tar -zcvf $DATETIME.tar.gz $DATETIME
#删除备份目录
rm -rf ${BACKUP}/$DATETIME

#删除10天前备份的数据
find $BACKUP -mtime +10 -name "*.tar.gz" -exec rm -rf {} \;
echo "===备份成功==="