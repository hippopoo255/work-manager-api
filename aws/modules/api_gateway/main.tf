module "upload_image" {
  source            = "./upload_image"
  origin_white_list = "http://localhost:3000,https://${local.env_types[data.aws_default_tags.this.tags.Env].hosts.app}.${local.domain_name}"
  storage_url       = "https://asset.${local.domain_name}"
}

resource "aws_api_gateway_rest_api" "this" {
  for_each = { for endpoint in local.endpoints : endpoint.name => endpoint }

  name = "${local.pj_name_kebab}-${each.key}-${data.aws_default_tags.this.tags.Env}"
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
  stage_name  = data.aws_default_tags.this.tags.Env

  # API gatewayのリソースを更新してもデプロイはされない
  # 再デプロイのトリガーとなる設定
  triggers = {
    redeployment = md5(file("${path.module}/${var.laravel_pj_root_path}/openapi/openapi.${each.key}.yaml"))
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
#
# resource "aws_api_gateway_account" "this" {
#   cloudwatch_role_arn = aws_iam_role.push_cloudwatch.arn
# }
#
# resource "aws_api_gateway_method_settings" "this" {
#   for_each = { for endpoint in local.endpoints : endpoint.name => endpoint }
#
#   depends_on = [
#     aws_api_gateway_account.this,
#   ]
#
#   rest_api_id = "${aws_api_gateway_rest_api.this[each.key].id}"
#   stage_name  = data.aws_default_tags.this.tags.Env
#   method_path = "*/*"
#
#   settings {
#     data_trace_enabled     = true
#     logging_level          = "INFO"
#   }
# }
