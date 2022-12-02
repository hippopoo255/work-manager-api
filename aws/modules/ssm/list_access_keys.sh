#! /bin/bash

mv $PATH_MODULE/s3-access-key-list.json $PATH_MODULE/s3-access-key-list_backup.json

aws iam list-access-keys \
--user-name $AWS_IAM_USER_NAME > $PATH_MODULE/s3-access-key-list.json