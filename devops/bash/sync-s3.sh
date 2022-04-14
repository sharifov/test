#!/bin/bash
# Sync s3 buckets
#
# Example:
#   sync-s3.sh s3://dev-crm-storage-data/
#

dotenv="/var/www/crm/.env"
if [ ! -e "$dotenv" ]; then
    echo "Error: can't locate $dotenv"
    exit 1
fi

if [ -z "$1" ]; then
    echo "Usage: $(basename $0) SRC_BUCKET"
    exit 1
fi

bucket=$(grep _S3_BUCKET $dotenv |cut -f 2 -d =)
key=$(grep _S3_CREDENTIALS_KEY $dotenv |cut -f 2 -d =)
secret=$(grep _S3_CREDENTIALS_SECRET $dotenv |cut -f 2 -d =)

export AWS_ACCESS_KEY_ID=$key
export AWS_SECRET_ACCESS_KEY=$secret
export AWS_DEFAULT_REGION=us-east-1

aws s3 sync $1 s3://$bucket
