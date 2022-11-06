provider "aws" {
  region = "ap-northeast-1"
}

# cdn
module "cdn" {
  source      = "./cdn"
  domain_name = var.domain_name
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

# RDS
module "rds" {
  source = "./rds"
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
  source            = "./cognito"
  pj_name_kebab     = var.pj_name_kebab
  pj_name_kana      = var.pj_name_kana
  pj_name_camel     = var.pj_name_camel
  test_email        = var.test_email
  test_username     = var.test_username
  test_userpassword = var.test_userpassword
}

# API Gateway
module "api_gateway" {
  source                      = "./api_gateway"
  http_uri                    = module.alb.http_alb_uri
  api_name_app                = "${var.pj_name_kebab}-app"
  api_name_admin              = "${var.pj_name_kebab}-admin"
  cognito_user_pool_arn_app   = module.cognito.userpool_arn_app
  cognito_user_pool_arn_admin = module.cognito.userpool_arn_admin
  domain_name                 = var.domain_name
}

# CloudWatch
module "cloudwatch" {
  source         = "./logs"
  name_prefix    = var.pj_name_snake
  retention_days = 3
}

# store parameters
module "ssm_parameter" {
  source                  = "./ssm"
  domain_name             = var.domain_name
  pj_name_kana            = var.pj_name_kana
  pj_name_kebab           = var.pj_name_kebab
  cognito_test_user_app   = module.cognito.test_user_app
  cognito_test_user_admin = module.cognito.test_user_admin
  rds_credentials = {
    db_user = var.db_user
    db_pass = var.db_pass
  }

  depends_on = [module.rds]
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
  # my_ecs_role_arn = module.role.my_ecs_role_arn
  log_groups = [
    module.cloudwatch.log_group_name_web,
    module.cloudwatch.log_group_name_app,
    module.cloudwatch.log_group_name_supervisor
  ]
}