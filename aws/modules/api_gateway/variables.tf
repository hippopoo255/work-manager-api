variable "cognito_user_pool_arn_app" {
  type    = string
  default = "userpool-admin"
}

variable "cognito_user_pool_arn_admin" {
  type    = string
  default = "userpool-admin"
}

variable "api_resource_name" {
  type    = string
  default = "blog_asset"
}

variable "laravel_pj_root_path" {
  type    = string
}