# ECSのターゲットグループに使う
output "target_group_front_arn" {
  value = aws_lb_target_group.front.arn
}
output "target_group_back_arn" {
  value = aws_lb_target_group.back.arn
}

output "security_group_http_id" {
  value = module.sg_http.security_group_id
}
output "http_alb_uri" {
  value = aws_route53_record.default.name
}