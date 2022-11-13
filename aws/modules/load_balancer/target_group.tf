/**
 * Laravelコンテナ用のターゲットグループ
 * ECSのサービス作成時にこのターゲットグループを選択することで、
 * Laravel用のコンテナインスタンスをこのターゲットグループに所属させることができる
 */
# Laravelコンテナ用のターゲットグループ
resource "aws_lb_target_group" "back" {
  for_each = { for g in local.target_group_list : g.name => g }

  name                 = "${local.pj_name_kebab}-tg-back-${each.key}"
  target_type          = "instance"
  vpc_id               = var.vpc_id
  port                 = 80
  protocol             = "HTTP"
  deregistration_delay = 300

  health_check {
    path                = "/api"
    healthy_threshold   = 2
    unhealthy_threshold = 5
    timeout             = 10
    interval            = 100
    matcher             = 200
    port                = "traffic-port"
    protocol            = "HTTP"
  }
}