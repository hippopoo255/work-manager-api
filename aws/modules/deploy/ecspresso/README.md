## Set up / Deploy manually

```bash:
$ brew install kayac/tap/ecspresso
$ cd prod
$ SYSTEM_NAME=foo-bar \
  ENV_NAME=prod \
  AWS_ECS_CLUSTER_NAME=FooBarCluster \
  AWS_ECS_BACK_SERVICE_NAME=foo-bar-service \
  AWS_REGION=ap-northeast-1 \
  ecspresso deploy --config=config_prod.yml
```

## See more information

- [https://github.com/kayac/ecspresso](https://github.com/kayac/ecspresso)
- [https://circleci.com/developer/orbs/orb/fujiwara/ecspresso](https://circleci.com/developer/orbs/orb/fujiwara/ecspresso)
- [https://twitter.com/fujiwara](https://twitter.com/fujiwara)
