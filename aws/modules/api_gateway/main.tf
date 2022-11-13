module "upload_image" {
  source            = "./upload_image"
  origin_white_list = "http://localhost:3000,https://www.${local.domain_name}"
  storage_url       = "https://asset.${local.domain_name}"
}

data "aws_caller_identity" "self" {}

data "template_file" "this" {
  for_each = { for endpoint in local.endpoints : endpoint.name => endpoint }

  template = file("${path.module}/schema/openapi.${each.key}.yaml")
  vars = {
    # openapi.admin.yamlファイルに${alb_uri}とあれば、terraformから当該箇所に値を埋め込むことができる
    alb_uri        = "https://${data.terraform_remote_state.init.outputs.http_alb_uri}"
    api_name       = "${local.pj_name_kebab}-${each.key}"
    aws_account_id = data.aws_caller_identity.self.account_id
    userpool_arns  = each.value.userpool_arns
  }
}

resource "aws_api_gateway_rest_api" "this" {
  for_each = { for endpoint in local.endpoints : endpoint.name => endpoint }

  name = "${local.pj_name_kebab}-${each.key}"
  body = data.template_file.this[each.key].rendered

  endpoint_configuration {
    types = ["EDGE"]
  }

  lifecycle {
    ignore_changes = [
      policy
    ]
  }
}

resource "aws_api_gateway_deployment" "this" {
  for_each = { for endpoint in local.endpoints : endpoint.name => endpoint }

  depends_on  = [aws_api_gateway_rest_api.this]
  rest_api_id = aws_api_gateway_rest_api.this[each.key].id
  stage_name  = var.env_name

  # API gatewayのリソースを更新してもデプロイはされない
  # 再デプロイのトリガーとなる設定
  triggers = {
    redeployment = md5(file("${path.module}/schema/openapi.${each.key}.yaml"))
    userpool_arn = md5(each.value.userpool_arns)
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
  source_arn    = "${aws_api_gateway_rest_api.this["app"].execution_arn}/*/*/${var.api_resource_name}"

  depends_on = [aws_api_gateway_deployment.this["app"]]
}