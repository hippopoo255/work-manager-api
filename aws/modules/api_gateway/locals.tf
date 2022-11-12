locals {
  endpoints = [
    {
      name          = "app",
      userpool_arns = var.cognito_user_pool_arn_app
    },
    {
      name          = "admin",
      userpool_arns = var.cognito_user_pool_arn_admin
    },
  ]
}