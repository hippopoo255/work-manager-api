data "aws_route53_zone" "this" {
  name = local.domain_name
}

module "sg_https" {
  source      = "../security_group"
  name        = "${local.pj_name_kebab}-https-sg"
  vpc_id      = var.vpc_id
  from_port   = "443"
  to_port     = "443"
  cidr_blocks = ["0.0.0.0/0"]
}

module "sg_http" {
  source      = "../security_group"
  name        = "${local.pj_name_kebab}-http-sg"
  vpc_id      = var.vpc_id
  from_port   = "0"
  to_port     = "65535"
  cidr_blocks = ["10.0.0.0/16"]
}

module "sg_http_redirect" {
  source      = "../security_group"
  name        = "${local.pj_name_kebab}-http-redirect-sg"
  vpc_id      = var.vpc_id
  from_port   = "8080"
  to_port     = "8080"
  cidr_blocks = ["0.0.0.0/0"]
}

resource "aws_lb" "this" {
  name               = "${local.pj_name_kebab}-alb"
  load_balancer_type = "application"
  internal           = false
  idle_timeout       = 60
  # destroy時は一旦falseにしてapply
  enable_deletion_protection = false

  # public0とpublic1
  subnets = var.subnets

  security_groups = [
    module.sg_https.security_group_id,
    module.sg_http.security_group_id,
    module.sg_http_redirect.security_group_id,
  ]
}

/**
 * リスナールール
 * 条件: リクエストパスがパスが""/api/*"であること
 * アクション: Laravelコンテナ用のターゲットグループに転送する
 */
resource "aws_lb_listener_rule" "back" {
  for_each = local.env_types

  listener_arn = aws_lb_listener.https.arn
  priority     = each.value.priority

  action {
    # Laravelコンテナ用のターゲットグループに転送するよう指定
    type             = "forward"
    target_group_arn = aws_lb_target_group.back[each.key].arn
  }

  condition {
    # このリスナールールの条件: リクエストパスが""/api/*"であること
    path_pattern {
      values = ["/api/*"]
    }
  }

  condition {
    http_header {
      http_header_name = "origin"
      values = [
        for k, host in each.value.hosts : "https://${host}.${local.domain_name}"
      ]
    }
  }

  # depends_on = [aws_lb_target_group.back]
}

# Aレコードの作成
resource "aws_route53_record" "this" {
  zone_id = data.aws_route53_zone.this.zone_id
  name    = "api.${data.aws_route53_zone.this.name}"
  type    = "A"

  alias {
    name                   = aws_lb.this.dns_name
    zone_id                = aws_lb.this.zone_id
    evaluate_target_health = true
  }
}

