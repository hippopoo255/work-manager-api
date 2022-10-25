#!/bin/bash

aws cognito-idp admin-set-user-password --user-pool-id $1 \
  --username $2 \
  --password $3 \
  --permanent