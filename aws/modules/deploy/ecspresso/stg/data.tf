data "aws_default_tags" "this" {}

data "aws_ecr_repository" "web" {
  name = "${data.aws_default_tags.this.tags.System}/${data.aws_default_tags.this.tags.Env}/web"
}
data "aws_ecr_repository" "app" {
  name = "${data.aws_default_tags.this.tags.System}/${data.aws_default_tags.this.tags.Env}/app"
}
data "aws_ecr_repository" "supervisor" {
  name = "${data.aws_default_tags.this.tags.System}/${data.aws_default_tags.this.tags.Env}/supervisor"
}

data "aws_cloudwatch_log_group" "web" {
  name              = "${local.pj_name_snake}_web_log_${data.aws_default_tags.this.tags.Env}"
}
data "aws_cloudwatch_log_group" "app" {
  name              = "${local.pj_name_snake}_app_log_${data.aws_default_tags.this.tags.Env}"
}
data "aws_cloudwatch_log_group" "supervisor" {
  name              = "${local.pj_name_snake}_supervisor_log_${data.aws_default_tags.this.tags.Env}"
}

data "aws_lb_target_group" "this" {
  name               = "${data.aws_default_tags.this.tags.System}-tg-back-${data.aws_default_tags.this.tags.Env}"
}

data "aws_ecs_cluster" "this" {
  cluster_name = "${local.cluster_name}_${data.aws_default_tags.this.tags.Env}"
}

data "aws_ecs_service" "this" {
  cluster_arn = data.aws_ecs_cluster.this.arn
  service_name = "${data.aws_default_tags.this.tags.System}-service-back"
}

data "aws_iam_role" "ecs_task_role" {
  name               = "ecs-task-role-${data.aws_default_tags.this.tags.Env}"
}

data "aws_iam_policy" "AmazonEC2ContainerRegistryPowerUser" {
  arn = "arn:aws:iam::aws:policy/AmazonEC2ContainerRegistryPowerUser"
}

data "aws_iam_policy" "AmazonECSTaskExecutionRolePolicy" {
  arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}