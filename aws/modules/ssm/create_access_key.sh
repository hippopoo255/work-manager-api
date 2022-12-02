#! /bin/bash

mv $PATH_MODULE/s3-access-user-credentials.json $PATH_MODULE/s3-access-user-credentials_backup.json

aws iam create-access-key \
--user-name $AWS_IAM_USER_NAME > $PATH_MODULE/s3-access-user-credentials.json