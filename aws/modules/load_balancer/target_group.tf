/**
 * Laravelコンテナ用のターゲットグループ
 * ECSのサービス作成時にこのターゲットグループを選択することで、
 * Laravel用のコンテナインスタンスをこのターゲットグループに所属させることができる
 */
# Laravelコンテナ用のターゲットグループ
resource "aws_lb_target_group" "back" {
  for_each = local.env_types

  name                 = "${local.pj_name_kebab}-tg-back-${each.key}"
  target_type          = "instance"
  vpc_id               = var.vpc_id
  port                 = 80
  protocol             = "HTTP"
  deregistration_delay = 300

  health_check {
    path                = "/api"
    healthy_threshold   = 2
    unhealthy_threshold = 6
    timeout             = 30
    interval            = 300
    matcher             = 200
    port                = "traffic-port"
    protocol            = "HTTP"
  }
}