output "vpc_id" {
  value = module.network.vpc_id
}

output "vpc_cidr_block" {
  value = module.network.vpc_cidr_block
}

output "subnet_ids" {
  value = module.network.subnet_public_ids

  depends_on = [module.network]
}