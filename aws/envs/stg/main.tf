# cdn
module "cdn" {
  source = "../../modules/cdn"
}

# RDS
module "rds" {
  source  = "../../modules/rds"
  db_user = var.db_user
  db_pass = var.db_pass
}

# Cognito
module "cognito" {
  source            = "../../modules/cognito"
  test_email        = var.test_email
  test_username     = var.test_username
  test_userpassword = var.test_userpassword
  env_name          = var.env_name
}

# # API Gateway
module "api_gateway" {
  source                      = "../../modules/api_gateway"
  cognito_user_pool_arn_app   = module.cognito.userpool_arn_app
  cognito_user_pool_arn_admin = module.cognito.userpool_arn_admin
  env_name                    = var.env_name
}

# # CloudWatch
module "cloudwatch" {
  source = "../../modules/logs"
  # デフォルト値を変更したい時はここのコメントアウトを外す
  # retention_days = 3
}

# store parameters
module "ssm_parameter" {
  source                  = "../../modules/ssm"
  cognito_test_user_app   = module.cognito.test_user_app
  cognito_test_user_admin = module.cognito.test_user_admin
  rds_credentials = {
    db_user = var.db_user
    db_pass = var.db_pass
  }
  env_name = var.env_name

  depends_on = [module.rds]
}

# ECR
module "ecr" {
  source   = "../../modules/ecr"
  env_name = var.env_name
}

# ECS
module "ecs" {
  source = "../../modules/orchestration"
  log_groups = [
    module.cloudwatch.log_group_name_web,
    module.cloudwatch.log_group_name_app,
    module.cloudwatch.log_group_name_supervisor
  ]
  env_name   = var.env_name
  depends_on = [module.ecr]
}

# frontend_dns_record
module "frontend_dns_record" {
  source                 = "../../modules/dns_record"
  front_hosting_settings = var.front_hosting_settings
}