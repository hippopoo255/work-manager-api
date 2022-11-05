# module "dynamodb"
# ...


data "aws_caller_identity" "self" {}

module "lambda" {
  source = "./lambda"
}

# AssumeRoleポリシー
data "aws_iam_policy_document" "this" {
  statement {
    actions = ["sts:AssumeRole"]

    principals {
      type        = "Service"
      identifiers = ["appsync.amazonaws.com"]
    }
  }
}

# Data source にLambdaを使うのでlambda invokeのポリシー
resource "aws_iam_policy" "this" {
  for_each = { for item in local.functions : item.name => item }

  name = "datasource-lambda-invoke--${each.value.name}"
  policy = templatefile("${path.module}/role/datasource_lambda_invoke.json", {
    account_id = data.aws_caller_identity.self.account_id,
    func_name  = each.value.name
  })
}

# ロール
resource "aws_iam_role" "this" {
  for_each = { for item in local.functions : item.name => item }

  name               = "appsync-datasource-role--${each.value.name}"
  assume_role_policy = data.aws_iam_policy_document.this.json
}

resource "aws_iam_role_policy_attachment" "this" {
  for_each = { for item in local.functions : item.name => item }

  role       = aws_iam_role.this[each.key].name
  policy_arn = aws_iam_policy.this[each.key].arn
}

# module.lambdaで作成できた Lambda リソース
data "aws_lambda_function" "this" {
  for_each = { for item in local.functions : item.name => item }

  function_name = each.key
}

# データソース
resource "aws_appsync_datasource" "this" {
  for_each = aws_iam_role.this

  api_id           = aws_appsync_graphql_api.this.id
  name             = "ds_${each.key}"
  service_role_arn = each.value.arn
  type             = "AWS_LAMBDA"

  lambda_config {
    function_arn = data.aws_lambda_function.this[each.key].arn
  }
}