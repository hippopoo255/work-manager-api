# APIサーバ用タスク定義
resource "aws_ecs_task_definition" "this" {
  family                   = "${local.pj_name_kebab}-task-${data.aws_default_tags.this.tags.Env}"
  network_mode             = "bridge"
  execution_role_arn       = aws_iam_role.ecs_task_role.arn
  requires_compatibilities = []
  # compatibilities = ["EC2"]
  container_definitions = templatefile("${path.module}/container_definition_back.json", {
    log_groups = var.log_groups,
    image_urls = [
      data.aws_ecr_repository.web.repository_url,
      data.aws_ecr_repository.app.repository_url,
      data.aws_ecr_repository.supervisor.repository_url
    ]
    namespace      = data.aws_default_tags.this.tags.Env
    default_region = local.default_region
  })
}

resource "aws_launch_configuration" "this" {
  # name: コンソール画面からクラスターテンプレート「EC2 Linux + ネットワーキング」を使用した場合のデフォルト値を踏襲
  # 複数回applyすると"AlreadyExists: Launch Configuration by this name already exists"というエラーになるので、name_prefixを使う
  name_prefix          = "EC2ContainerService-${local.cluster_name}_${data.aws_default_tags.this.tags.Env}-EcsInstanceLc"
  image_id             = data.aws_ssm_parameter.amzn2_for_ecs_ami.value
  instance_type        = "t2.micro"
  security_groups      = [data.terraform_remote_state.init.outputs.security_group_http_id]
  key_name             = "web-server"
  iam_instance_profile = aws_iam_instance_profile.ecs_instance_profile.id

  user_data = base64encode(templatefile("${path.module}/userdata.sh", {
    cluster_name = "${local.cluster_name}_${data.aws_default_tags.this.tags.Env}"
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

resource "aws_autoscaling_group" "this" {

  # name: コンソール画面からクラスターテンプレート「EC2 Linux + ネットワーキング」を使用した場合のデフォルト値を踏襲
  name                  = "EC2ContainerService-${local.cluster_name}_${data.aws_default_tags.this.tags.Env}-EcsInstanceAsg"
  launch_configuration  = aws_launch_configuration.this.name
  vpc_zone_identifier   = [data.terraform_remote_state.init.outputs.subnet_ids.1]
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
resource "aws_ecs_cluster" "this" {
  name = "${local.cluster_name}_${data.aws_default_tags.this.tags.Env}"
}

resource "aws_ecs_service" "back_service" {
  name                              = "${local.pj_name_kebab}-service-back"
  cluster                           = aws_ecs_cluster.this.id
  task_definition                   = aws_ecs_task_definition.this.arn
  desired_count                     = 1
  health_check_grace_period_seconds = 30
  launch_type                       = "EC2"
  iam_role                          = "arn:aws:iam::${data.aws_caller_identity.self.account_id}:role/aws-service-role/ecs.amazonaws.com/AWSServiceRoleForECS"

  depends_on = [aws_ecs_cluster.this]

  load_balancer {
    target_group_arn = data.terraform_remote_state.init.outputs.target_group_back_arn[data.aws_default_tags.this.tags.Env]
    container_name   = "web"
    container_port   = 80
  }

  lifecycle {
    ignore_changes = [
      # task_definition,
      #       desired_count,
      #       capacity_provider_strategy,
      #       # ブルーグリーン入れる場合はこちらを有効にする(ターゲットグループが切り替わるため)
      #       # load_balancer, 
    ]
  }
}