variable "log_groups" {
  type = list(any)
}

variable "env_name" {
  type    = string
  default = "stg" # prod / stg
}