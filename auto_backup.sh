HOST=easy_principal.mysql.dbaas.com.br
USER=easy_principal
PASSWORD='w7m52H0d#*x$vH'
DATABASE=easy_principal
DATE=`date +"%Y%m%d"`
SQLFILE=bkp_${DATABASE}_${DATE}.sql

DATE_AGO=$(date --date="1 months ago" +"%Y%m%d")
rm bkp_${DATABASE}_${DATE_AGO}.sql

mysqldump -h ${HOST} -u ${USER} --password=${PASSWORD} -R --opt --routines --triggers --no-tablespaces ${DATABASE} > ${SQLFILE}