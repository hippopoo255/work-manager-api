locals {
  functions = [
    { name = "delete_blog", env = "" },
    { name = "get_blog_by_id", env = "" },
    { name = "get_blogs", env = "" },
    { name = "get_tags", env = "" },
    { name = "save_blog", env = "" },
    # { name = "upload_image", env="" },
  ]
}

data "aws_caller_identity" "self" {}

# assume_role = ロールの「信頼」タブ参照
data "aws_iam_policy_document" "lambda_assume_role_policy" {
  statement {
    actions = ["sts:AssumeRole"]

    principals {
      type        = "Service"
      identifiers = ["lambda.amazonaws.com"]
    }
  }
}

data "aws_iam_policy" "AmazonDynamoDBFullAccess" {
  arn = "arn:aws:iam::aws:policy/AmazonDynamoDBFullAccess"
}

resource "aws_iam_policy" "lambda_basic_executions" {
  for_each = { for i in local.functions : i.name => i }

  name = "lambda-basic-execution--${each.value.name}"
  policy = templatefile("${path.module}/roles/lambda_execution.json", {
    account_id = data.aws_caller_identity.self.account_id,
    func_name  = each.value.name
  })
}

resource "aws_iam_role" "lambda_roles" {
  for_each = { for i in local.functions : i.name => i }

  name                = "lambda-role--${each.value.name}"
  assume_role_policy  = data.aws_iam_policy_document.lambda_assume_role_policy.json
  managed_policy_arns = [data.aws_iam_policy.AmazonDynamoDBFullAccess.arn, aws_iam_policy.lambda_basic_executions[each.key].arn]
}

# Zip化
data "archive_file" "lambda_functions" {
  for_each = { for i in local.functions : i.name => i }

  type        = "zip"
  source_dir  = "${path.module}/${each.key}"
  output_path = "${path.module}/${each.key}/lambda_function.zip"
}

resource "aws_lambda_function" "lambda_functions" {
  for_each = aws_iam_role.lambda_roles

  filename      = data.archive_file.lambda_functions[each.key].output_path
  function_name = each.key
  role          = each.value.arn
  # ref: ランタイム設定
  handler = "lambda_function.lambda_handler"
  # See https://docs.aws.amazon.com/lambda/latest/dg/API_CreateFunction.html#SSS-CreateFunction-request-Runtime
  runtime          = "python3.9"
  timeout          = 30
  source_code_hash = data.archive_file.lambda_functions[each.key].output_base64sha256
}