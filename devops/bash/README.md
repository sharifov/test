Handy Shell Scirpts
=====================================

These scritps are designed to run on an application servers.


# MySQL database migration

Export MySQL dump:
```
ssh ubuntu@current-app-host
export-mysql.sh |gzip > /tmp/mysql.sql.gz
```

Import MySQL dump:
```
ssh ubuntu@new-app-host
scp ubuntu@current-app-host:/tmp/mysql.sql /tmp/
gunzip < /tmp/mysql.sql.gz |import-mysql.sh
```


# PostgreSQL database migration

Export PostgreSQL dump:
```
ssh ubuntu@current-app-host
export-pgsql.sh |gzip > /tmp/pgsql.sql.gz
```

Import PostgreSQL dump:
```
ssh ubuntu@new-app-host
scp ubuntu@current-app-host:/tmp/pgsql.sql /tmp/
gunzip < /tmp/pgsql.sql.gz |import-pgsql.sh
```

# S3 bucket synchronization
Make sure that IAM account has access to src S3 bucket
```
ssh ubuntu@new-app-host
sync-s3.sh s3://dev-storage-data/
```
