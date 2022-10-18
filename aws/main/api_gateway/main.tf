data "aws_caller_identity" "self" {}

data "aws_cognito_user_pools" "default" {
  name = var.cognito_user_pool_name_default
}

data "aws_cognito_user_pools" "admin" {
  name = var.cognito_user_pool_name_admin
}

data "template_file" "rest_app" {
  template = file("${var.json_path_prefix}/schema/openapi.app.yaml")
  vars = {
    # openapi.app.yamlファイルに${alb_uri}とあれば、terraformから当該箇所に値を埋め込むことができる
    alb_uri        = "https://${var.http_uri}"
    api_name   = var.api_name_app
    aws_account_id = data.aws_caller_identity.self.account_id
    userpool_arns  = data.aws_cognito_user_pools.default.arns.0
  }
}

data "template_file" "rest_admin" {
  template = file("${var.json_path_prefix}/schema/openapi.admin.yaml")
  vars = {
    # openapi.admin.yamlファイルに${alb_uri}とあれば、terraformから当該箇所に値を埋め込むことができる
    alb_uri        = "https://${var.http_uri}"
    api_name = var.api_name_admin
    aws_account_id = data.aws_caller_identity.self.account_id
    userpool_arns  = data.aws_cognito_user_pools.admin.arns.0
  }
}

resource "aws_api_gateway_rest_api" "api_app" {
  name = var.api_name_app
  body = data.template_file.rest_app.rendered

  endpoint_configuration {
    types = ["EDGE"]
  }

  lifecycle {
    ignore_changes = [
      policy
    ]
  }
}

resource "aws_api_gateway_rest_api" "api_admin" {
  name = var.api_name_admin
  body = data.template_file.rest_admin.rendered

  endpoint_configuration {
    types = ["EDGE"]
  }

  lifecycle {
    ignore_changes = [
      policy
    ]
  }
}

resource "aws_api_gateway_deployment" "deployment_app" {
  depends_on  = [aws_api_gateway_rest_api.api_app]
  rest_api_id = aws_api_gateway_rest_api.api_app.id
  stage_name  = "develop"

  # API gatewayのリソースを更新してもデプロイはされない
  # 再デプロイのトリガーとなる設定
  triggers = {
    redeployment = sha1(file("${var.json_path_prefix}/schema/openapi.app.yaml"))
  }

  lifecycle {
    create_before_destroy = true
  }
}

resource "aws_api_gateway_deployment" "deployment_admin" {
  depends_on  = [aws_api_gateway_rest_api.api_admin]
  rest_api_id = aws_api_gateway_rest_api.api_admin.id
  stage_name  = "develop"

  # API gatewayのリソースを更新してもデプロイはされない
  # 再デプロイのトリガーとなる設定
  triggers = {
    redeployment = sha1(file("${var.json_path_prefix}/schema/openapi.admin.yaml"))
  }

  lifecycle {
    create_before_destroy = true
  }
}