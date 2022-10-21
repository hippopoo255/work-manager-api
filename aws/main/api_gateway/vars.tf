variable "cognito_user_pool_name_default" {
  type    = string
  default = "userpool-user"
}

variable "cognito_user_pool_name_admin" {
  type    = string
  default = "userpool-admin"
}

variable "json_path_prefix" {
  type    = string
  default = "."
}

variable "http_uri" {
  type    = string
  default = "example.com"
}

variable "api_name_app" {
  type    = string
  default = "pj-name-kebab-app"
}

variable "api_name_admin" {
  type    = string
  default = "pj-name-kebab-admin"
}