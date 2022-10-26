variable "domain_name" {
  type    = string
  default = "work-manager.site"
}

resource "aws_route53_zone" "default" {
  name = var.domain_name
}
