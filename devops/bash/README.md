CRM: Handy Shell Scirpts
=====================================

These scritps are designed to run on an application servers.


# MySQL database migration

Export MySQL dump:
```
ssh ubuntu@current-app-host
export-mysql.sh |gzip > /tmp/crm-mysql.sql.gz
```

Import MySQL dump:
```
ssh ubuntu@new-app-host
gunzip < /tmp/crm-mysql.sql.gz |import-mysql.sh
```


# PostgreSQL database migration

Export PostgreSQL dump:
```
ssh ubuntu@current-app-host
export-pgsql.sh |gzip > /tmp/crm-pgsql.sql.gz
```

Import PostgreSQL dump:
```
ssh ubuntu@new-app-host
gunzip < /tmp/crm-pgsql.sql.gz |import-pgsql.sh
```

# S3 bucket synchronization
In this example we will allow attachments-crm-dev user from Dev account 
to access S3 bucket named dev-crm-storage-data from Prod account
The following policy needs to be attached to dev-crm-storage-data bucket:
```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Principal": {
                "AWS": "arn:aws:iam::242502574742:user/attachments-crm-dev"
            },
            "Action": "s3:ListBucket",
            "Resource": "arn:aws:s3:::dev-crm-storage-data"
        },
        {
            "Effect": "Allow",
            "Principal": {
                "AWS": "arn:aws:iam::242502574742:user/attachments-crm-dev"
            },
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::dev-crm-storage-data/*"
        }
    ]
```
The following policy should be manifested in iam.tf

```
data "aws_iam_policy_document" "s3_attachments_write_permissions" {
  statement {
    effect = "Allow"

    actions = [
      "s3:Get*",
      "s3:List*",
      "s3:AbortMultipartUpload",
      "s3:ListMultipartUploadParts",
      "s3:PutObject",
      "s3:DeleteObject"
    ]

    resources = [
      "arn:aws:s3:::attachments-${var.PROJECT}-${var.ENV}",
      "arn:aws:s3:::attachments-${var.PROJECT}-${var.ENV}/*",
      "arn:aws:s3:::dev-crm-storage-data",
      "arn:aws:s3:::dev-crm-storage-data/*"
    ]
  }
}
```

When this is done you can run sync-s3.sh on the new application server

```
ssh ubuntu@new-app-host
sync-s3.sh s3://dev-crm-storage-data/
```
