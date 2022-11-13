module "load_balancer" {
  source = "../../../modules/load_balancer"

  vpc_id = var.vpc_id
  # networkディレクトリのoutput.tfで出力したやつを使う
  subnets = var.subnets
}