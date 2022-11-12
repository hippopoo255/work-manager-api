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