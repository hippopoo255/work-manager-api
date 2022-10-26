#!/bin/bash

aws cognito-idp admin-set-user-password --user-pool-id $COGNITO_USERPOOL_ID \
  --username $COGNITO_USERNAME \
  --password $COGNITO_PASSWORD \
  --permanent