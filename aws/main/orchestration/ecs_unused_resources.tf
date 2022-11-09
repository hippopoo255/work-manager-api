/*
フロント用タスク定義
フロントエンドはコンテナ管理から外し、タスク定義しない
- t2.microその他無料利用枠のインスタンスタイプではスペックが不足するため
- Next.jsのPJをVercelにデプロイする方法が無料のため
*/

# data "aws_ecr_repository" "web" {
#   name = "${var.name_prefix}/web"
# }
# 
# data "aws_ecr_repository" "app" {
#   name = "${var.name_prefix}/app"
# }
# 
# data "aws_ecr_repository" "supervisor" {
#   name = "${var.name_prefix}/supervisor"
# }
# 
# resource "aws_ecs_task_definition" "front" {
#   family = "${var.name_prefix}-task-front"
#   network_mode = "bridge"
#   execution_role_arn = var.my_ecs_role_arn
#   requires_compatibilities = []
#   # compatibilities = ["EC2"]
#   container_definitions = templatefile("./orchestration/container_definition_front.json", {
#     log_groups = var.log_groups
#   })
# }

/*
aws_launch_template
If you want to use "aws_launch_template" instead of "aws_launch_configuration", activate this block
*/

# resource "aws_launch_template" "default" {
#   name          = "EC2ContainerService-${var.cluster_name}-EcsInstanceLc"
#   image_id      = data.aws_ssm_parameter.amzn2_for_ecs_ami.value
#   instance_type = "t2.micro"
#   ebs_optimized = true
#   user_data = base64encode(templatefile("${path.module}/userdata.sh", {
#     cluster_name = "${var.cluster_name}"
#   }))
#   key_name = "web-server"
# 
#   network_interfaces {
#     associate_public_ip_address = true
#     security_groups = [var.security_group_id]
#     subnet_id = var.subnets.0
#   }
# 
#   block_device_mappings {
#     device_name = "/dev/sda1"
#     ebs {
#       volume_size = 30
#       volume_type = "gp2"
#     }
#   }
# }

/**
  auto scaling groups using resource "aws_launch_template" instead of "aws_launch_configuration"
*/
# resource "aws_autoscaling_group" "default" {
#   name = "EC2ContainerService-${var.cluster_name}-EcsInstanceAsg"
# # # If you want to use "aws_launch_template" instead of "aws_launch_configuration", activate this block
#   launch_template {
#     id = aws_launch_template.default.id
#     version = "$Latest"
#   }
#   vpc_zone_identifier = [var.subnets.1]
#   protect_from_scale_in = false
#   health_check_type         = "EC2"
#   # wait_for_capacity_timeout = 300  # TerraformがAuto Scalingグループのインスタンスの作成を待機しないようにする
#   max_size = 1
#   min_size = 0
#   desired_capacity = 1
# 
#   lifecycle {
#     create_before_destroy = true
#   }
# }

# resource "aws_ecs_capacity_provider" "default" {
#   name = "ec2"
# 
#   auto_scaling_group_provider {
#     auto_scaling_group_arn = aws_autoscaling_group.default.arn
#     managed_termination_protection = "ENABLED"
# 
#     managed_scaling {
#       maximum_scaling_step_size = 1
#       minimum_scaling_step_size = 1
#       status = "ENABLED"
#       target_capacity = 100
#       # instance_warmup_period    = 300
#     }
#   }
# }
# 
# resource "aws_ecs_cluster" "default" {
#   name = "${var.cluster_name}"
#   # capacity_providers = [aws_ecs_capacity_provider.default.name]
# 
#   # default_capacity_provider_strategy {
#   #   capacity_provider = aws_ecs_capacity_provider.default.name
#   #   weight = 1
#   #   base = 0
#   # }
# }

# resource "aws_ecs_cluster_capacity_providers" "default" {
#   cluster_name = "${var.cluster_name}"
#   capacity_providers = [aws_ecs_capacity_provider.default.name]
# 
#   default_capacity_provider_strategy {
#     base              = 0
#     weight            = 1
#     capacity_provider = aws_ecs_capacity_provider.default.name
#   }
# }
# 

# resource "aws_appautoscaling_target" "ecs_service" {
#   max_capacity       = 2
#   min_capacity       = 1
#   resource_id        = "service/${aws_ecs_cluster.default.name}/${aws_ecs_service.back_service.name}"
#   scalable_dimension = "ecs:service:DesiredCount"
#   service_namespace  = "ecs"
# }