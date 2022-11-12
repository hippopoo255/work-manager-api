output "target_group_back_arn" {
  value = module.load_balancer.target_group_back_arn
}

output "security_group_http_id" {
  value = module.load_balancer.security_group_http_id
}

output "http_alb_uri" {
  value = module.load_balancer.http_alb_uri
}