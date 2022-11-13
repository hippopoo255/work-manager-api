resource "aws_vpc" "this" {
  cidr_block           = local.vpc_cidr_block
  enable_dns_support   = true
  enable_dns_hostnames = true

  tags = {
    Name = local.pj_name_kebab
  }
}
