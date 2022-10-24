provider "aws" {
  region = "ap-northeast-1"
}

# ECSでSSMのパラメータストアのパラメータにアクセスする等のロール
module "role" {
  source           = "./role"
  json_path_prefix = "./role"
}

# cdn
module "cdn" {
  source           = "./cdn"
  domain_name      = var.domain_name
  json_path_prefix = "./cdn"
}

# network
module "network" {
  source         = "./network"
  vpc_tag_name   = var.pj_name_kebab
  rtb_tag_name   = var.pj_name_snake
  vpc_cidr_block = "10.0.0.0/16"
}

# ALB
module "alb" {
  source = "./load_balancer"
  vpc_id = module.network.vpc_id
  # networkディレクトリのoutput.tfで出力したやつを使う
  subnets = [
    module.network.subnet_public0_id,
    module.network.subnet_public1_id,
  ]
  domain_name    = var.domain_name
  sg_name_prefix = var.pj_name_kebab
}

# CloudWatch
module "cloudwatch" {
  source         = "./logs"
  name_prefix    = var.pj_name_snake
  retention_days = 3
}

# ECR -> 3.ECRに移行(repo_name_prefixをvar.pj_name_kebabと合うように指定すること)
# module "ecr" {
#   source = "./ecr"
#   repo_name_prefix = var.pj_name_kebab
# }

# ECS
module "ecs" {
  source         = "./orchestration"
  vpc_id         = module.network.vpc_id
  vpc_cidr_block = module.network.vpc_cidr_block
  subnets = [
    module.network.subnet_public0_id,
    module.network.subnet_public1_id,
  ]
  target_group_front_arn = module.alb.target_group_front_arn
  target_group_back_arn  = module.alb.target_group_back_arn
  security_group_id      = module.alb.security_group_http_id
  name_prefix            = var.pj_name_kebab
  # cluster_name = var.cluster_name
  # front_service_name = var.front_service_name
  # back_service_name = var.back_service_name
  my_ecs_role_arn = module.role.my_ecs_role_arn
  log_groups = [
    module.cloudwatch.log_group_name_web,
    module.cloudwatch.log_group_name_app,
    module.cloudwatch.log_group_name_supervisor
  ]
}

# RDS
module "mysql_rds" {
  source = "./db"
  subnet_ids = [
    module.network.subnet_public0_id,
    module.network.subnet_public1_id,
  ]
  vpc_id         = module.network.vpc_id
  vpc_cidr_block = module.network.vpc_cidr_block
  db_user        = var.db_user
  db_pass        = var.db_pass
  pj_name_kebab  = var.pj_name_kebab
  pj_name_snake  = var.pj_name_snake
}

# Cognito
module "cognito" {
  source        = "./cognito"
  pj_name_kebab = var.pj_name_kebab
  pj_name_kana  = var.pj_name_kana
  pj_name_camel = var.pj_name_camel
}

# TODO: 2つのuserpoolIDをoutputしてストアパラメータ(AMAZON_USER_COGNITO_POOL_ID, AMAZON_ADMIN_COGNITO_POOL_ID)として設定したい
# TODO: 作成したユーザープールにユーザ情報を追加してcognito-subをoutputしたい
# TODO: outputしたcognito-subをストアパラメータ(APP_TEST_USER_COGNITO_SUB, APP_TEST_ADMIN_COGNITO_SUB)として設定したい

# API Gateway
module "api_gateway" {
  source                      = "./api_gateway"
  json_path_prefix            = "./api_gateway"
  http_uri                    = module.alb.http_alb_uri
  api_name_app                = "${var.pj_name_kebab}-app"
  api_name_admin              = "${var.pj_name_kebab}-admin"
  cognito_user_pool_arn_app   = module.cognito.userpool_arn_app
  cognito_user_pool_arn_admin = module.cognito.userpool_arn_admin
}