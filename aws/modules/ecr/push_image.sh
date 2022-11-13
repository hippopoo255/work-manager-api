#! /bin/bash

aws ecr get-login-password \
--region $AWS_DEFAULT_REGION | docker login \
--username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$AWS_DEFAULT_REGION.amazonaws.com

cd $PJ_ROOT_PATH

docker build --no-cache -t $REPOSITORY_URL -f docker/$IMAGE_DIR/Dockerfile --build-arg TZ=Asia/Tokyo .

docker push $REPOSITORY_URL

cd -