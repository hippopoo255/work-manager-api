data "aws_ecr_repository" "web" {
  name = "${var.name_prefix}/web"
}

data "aws_ecr_repository" "app" {
  name = "${var.name_prefix}/app"
}

data "aws_ecr_repository" "supervisor" {
  name = "${var.name_prefix}/supervisor"
}

# APIサーバ用タスク定義
resource "aws_ecs_task_definition" "back" {
  family                   = "${var.name_prefix}-task-back"
  network_mode             = "bridge"
  execution_role_arn       = aws_iam_role.my_ecs_role.arn
  requires_compatibilities = []
  # compatibilities = ["EC2"]
  container_definitions = templatefile("./orchestration/container_definition_back.json", {
    log_groups = var.log_groups,
    image_urls = [
      data.aws_ecr_repository.web.repository_url,
      data.aws_ecr_repository.app.repository_url,
      data.aws_ecr_repository.supervisor.repository_url
    ]
  })
}

# auto scaling group
data "aws_ssm_parameter" "amzn2_for_ecs_ami" {
  name = "/aws/service/ecs/optimized-ami/amazon-linux-2/recommended/image_id"
}

resource "aws_launch_configuration" "default" {
  # name: コンソール画面からクラスターテンプレート「EC2 Linux + ネットワーキング」を使用した場合のデフォルト値を踏襲
  # 複数回applyすると"AlreadyExists: Launch Configuration by this name already exists"というエラーになるので、name_prefixを使う
  name_prefix          = "EC2ContainerService-${var.cluster_name}-EcsInstanceLc"
  image_id             = data.aws_ssm_parameter.amzn2_for_ecs_ami.value
  instance_type        = "t2.micro"
  security_groups      = [var.security_group_id]
  key_name             = "web-server"
  iam_instance_profile = aws_iam_instance_profile.ecs_instance_profile.id

  user_data = base64encode(templatefile("${path.module}/userdata.sh", {
    cluster_name = "${var.cluster_name}"
  }))

  depends_on = [aws_iam_instance_profile.ecs_instance_profile]

  root_block_device {
    volume_size = "30"
    volume_type = "gp2"
  }

  lifecycle {
    create_before_destroy = true
  }
}

resource "aws_autoscaling_group" "default" {
  # name: コンソール画面からクラスターテンプレート「EC2 Linux + ネットワーキング」を使用した場合のデフォルト値を踏襲
  name                  = "EC2ContainerService-${var.cluster_name}-EcsInstanceAsg"
  launch_configuration  = aws_launch_configuration.default.name
  vpc_zone_identifier   = [var.subnets.1]
  protect_from_scale_in = false
  health_check_type     = "EC2"
  # wait_for_capacity_timeout = 300  # TerraformがAuto Scalingグループのインスタンスの作成を待機しないようにする
  max_size         = 1
  min_size         = 0
  desired_capacity = 1

  lifecycle {
    create_before_destroy = true
  }
}

# ECS Cluster
resource "aws_ecs_cluster" "default" {
  name = var.cluster_name
}

# ECS Service
data "aws_caller_identity" "self" {}

resource "aws_ecs_service" "back_service" {
  name                              = "${var.name_prefix}-service-back"
  cluster                           = aws_ecs_cluster.default.id
  task_definition                   = aws_ecs_task_definition.back.arn
  desired_count                     = 1
  health_check_grace_period_seconds = 30
  launch_type                       = "EC2"
  iam_role                          = "arn:aws:iam::${data.aws_caller_identity.self.account_id}:role/aws-service-role/ecs.amazonaws.com/AWSServiceRoleForECS"

  depends_on = [aws_ecs_cluster.default]

  load_balancer {
    target_group_arn = var.target_group_back_arn
    container_name   = "web"
    container_port   = 80
  }

  lifecycle {
    ignore_changes = [
      task_definition,
      #       desired_count,
      #       capacity_provider_strategy,
      #       # ブルーグリーン入れる場合はこちらを有効にする(ターゲットグループが切り替わるため)
      #       # load_balancer, 
    ]
  }
}