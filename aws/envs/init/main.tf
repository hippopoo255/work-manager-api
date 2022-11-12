# routing
# route53
module "host" {
  source = "../../modules/route53"
}

# acm
module "acm" {
  source = "../../modules/acm"
  providers = {
    aws.tokyo    = aws
    aws.virginia = aws.virginia
  }

  depends_on = [module.host]
}

# network
module "network" {
  source = "./network"
}

# load_balancer
module "load_balancer" {
  source = "./load_balancer"
  vpc_id = module.network.vpc_id
  # networkディレクトリのoutput.tfで出力したやつを使う
  subnets = [
    module.network.subnet_public0_id,
    module.network.subnet_public1_id,
  ]

  depends_on = [module.network]
}