# ECSのターゲットグループに使う
output "target_group_back_arn" {
  value = {
    # for_each = local.env_types
    prod = aws_lb_target_group.back["prod"].arn,
    stg  = aws_lb_target_group.back["stg"].arn,
  }
}

output "security_group_http_id" {
  value = module.sg_http.security_group_id
}

output "http_alb_uri" {
  value = aws_route53_record.this.name
}