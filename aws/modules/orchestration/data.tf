data "terraform_remote_state" "init" {
  backend = "s3"

  config = {
    bucket = "${local.pj_name_kebab}-tfstate"
    key    = "${local.pj_name_kebab}/init/main.tfstate"
    region = "${local.default_region}"
  }
}

# ECS Service
data "aws_caller_identity" "self" {}
data "aws_default_tags" "this" {}

data "aws_ecr_repository" "web" {
  name = "${local.pj_name_kebab}/${data.aws_default_tags.this.tags.Env}/web"
}

data "aws_ecr_repository" "app" {
  name = "${local.pj_name_kebab}/${data.aws_default_tags.this.tags.Env}/app"
}

data "aws_ecr_repository" "supervisor" {
  name = "${local.pj_name_kebab}/${data.aws_default_tags.this.tags.Env}/supervisor"
}

# auto scaling group
data "aws_ssm_parameter" "amzn2_for_ecs_ami" {
  name = "/aws/service/ecs/optimized-ami/amazon-linux-2/recommended/image_id"
}