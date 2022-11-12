variable "vpc_id" {
  type = string
}

variable "subnets" {
  type = list(string)
}

variable "env" {
  type    = string
  default = "prod"
}