output "vpc_id" {
  value = module.network.vpc_id
}

output "subnet_public0_id" {
  value = module.network.subnet_public0_id
}

output "subnet_public1_id" {
  value = module.network.subnet_public1_id
}

output "vpc_cidr_block" {
  value = module.network.vpc_cidr_block
}