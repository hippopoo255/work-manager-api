output "vpc_id" {
  value = module.network.vpc_id
}

output "vpc_cidr_block" {
  value = module.network.vpc_cidr_block
}

output "subnet_ids" {
  value = [
    module.network.subnet_public0_id,
    module.network.subnet_public1_id,
  ]
}

output "target_group_back_arn" {
  value = module.load_balancer.target_group_back_arn
}

output "security_group_http_id" {
  value = module.load_balancer.security_group_http_id
}

output "http_alb_uri" {
  value = module.load_balancer.http_alb_uri
}