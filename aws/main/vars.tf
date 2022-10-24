variable "pj_name_kebab" {
  type    = string
  default = "work-manager"
}

variable "pj_name_snake" {
  type    = string
  default = "work_manager"
}

variable "pj_name_camel" {
  type    = string
  default = "WorkManager"
}

variable "pj_name_kana" {
  type    = string
  default = "ジョブサポ"
}

variable "domain_name" {
  type    = string
  default = "work-manager.site"
}

variable "db_user" {}
variable "db_pass" {}
# variable "cluster_name" {}
# variable "front_service_name" {}
# variable "back_service_name" {}