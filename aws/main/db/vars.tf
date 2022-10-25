variable "subnet_ids" {}
variable "vpc_id" {}
variable "vpc_cidr_block" {}
variable "db_user" {}
variable "db_pass" {}
variable "db_connection" {
  type = string
  default = "mysql"
}
variable "db_port" {
  type = string
  default = "3306"
}
variable "pj_name_kebab" {}
variable "pj_name_snake" {}