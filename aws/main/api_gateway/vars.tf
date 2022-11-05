variable "cognito_user_pool_arn_app" {
  type    = string
  default = "userpool-admin"
}

variable "cognito_user_pool_arn_admin" {
  type    = string
  default = "userpool-admin"
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

variable "domain_name" {
  type    = string
  default = ""
}

variable "api_resource_name" {
  type = string
  default ="blog_asset"
}