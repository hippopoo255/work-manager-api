data "terraform_remote_state" "init" {
  backend = "s3"

  config = {
    bucket = "${local.pj_name_kebab}-tfstate"
    key    = "${local.pj_name_kebab}/init/main.tfstate"
    region = "${local.default_region}"
  }
}

data "aws_default_tags" "this" {}

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
    env_name       = data.aws_default_tags.this.tags.Env
    host           = local.env_types[data.aws_default_tags.this.tags.Env].hosts.app
    domain_name    = local.domain_name
  }
}

data "aws_iam_policy" "AmazonAPIGatewayPushToCloudWatchLogs" {
  arn = "arn:aws:iam::aws:policy/service-role/AmazonAPIGatewayPushToCloudWatchLogs"
}

# assume_role = ロールの「信頼」タブ参照
data "aws_iam_policy_document" "api_gateway_assume_role_policy" {
  statement {
    actions = ["sts:AssumeRole"]

    principals {
      type        = "Service"
      identifiers = ["apigateway.amazonaws.com"]
    }
  }
}