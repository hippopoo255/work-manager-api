module "upload_image" {
  source            = "./upload_image"
  origin_white_list = "http://localhost:3000,https://www.${var.domain_name}"
  storage_url       = "https://asset.${var.domain_name}"
}

data "aws_caller_identity" "self" {}

data "template_file" "rest_app" {
  template = file("${path.module}/schema/openapi.app.yaml")
  vars = {
    # openapi.app.yamlファイルに${alb_uri}とあれば、terraformから当該箇所に値を埋め込むことができる
    alb_uri        = "https://${var.http_uri}"
    api_name       = var.api_name_app
    aws_account_id = data.aws_caller_identity.self.account_id
    userpool_arns  = var.cognito_user_pool_arn_app
  }
}

data "template_file" "rest_admin" {
  template = file("${path.module}/schema/openapi.admin.yaml")
  vars = {
    # openapi.admin.yamlファイルに${alb_uri}とあれば、terraformから当該箇所に値を埋め込むことができる
    alb_uri        = "https://${var.http_uri}"
    api_name       = var.api_name_admin
    aws_account_id = data.aws_caller_identity.self.account_id
    userpool_arns  = var.cognito_user_pool_arn_admin
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
    redeployment = md5(file("${path.module}/schema/openapi.app.yaml"))
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
    redeployment = md5(file("${path.module}/schema/openapi.admin.yaml"))
  }

  lifecycle {
    create_before_destroy = true
  }
}

resource "aws_lambda_permission" "api_gateway_trigger" {
  statement_id  = "AllowExecutionFromApiGateway"
  action        = "lambda:InvokeFunction"
  function_name = module.upload_image.lambda_name
  principal     = "apigateway.amazonaws.com"
  source_arn    = "${aws_api_gateway_rest_api.api_app.execution_arn}/*/*/${var.api_resource_name}"

  depends_on = [aws_api_gateway_deployment.deployment_app]
}