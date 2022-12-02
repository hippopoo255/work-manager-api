#! /bin/bash

rm $PATH_MODULE/s3-access-user-credentials.json $PATH_MODULE/s3-access-key-list.json

mv $PATH_MODULE/s3-access-key-list_backup.json $PATH_MODULE/s3-access-key-list.json
mv $PATH_MODULE/s3-access-user-credentials_backup.json $PATH_MODULE/s3-access-user-credentials.json