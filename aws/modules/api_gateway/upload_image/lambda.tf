# Zip化
data "archive_file" "lambda_function" {
  type        = "zip"
  source_dir  = path.module
  output_path = "${path.module}/lambda_function.zip"
}

resource "aws_lambda_function" "upload_image" {
  filename      = data.archive_file.lambda_function.output_path
  function_name = "upload_image_${data.aws_default_tags.this.tags.Env}"
  role          = aws_iam_role.upload_image.arn
  # ref: ランタイム設定
  handler = "lambda_function.lambda_handler"
  # See https://docs.aws.amazon.com/lambda/latest/dg/API_CreateFunction.html#SSS-CreateFunction-request-Runtime
  runtime          = "python3.9"
  timeout          = 30
  source_code_hash = data.archive_file.lambda_function.output_base64sha256

  environment {
    variables = {
      ORIGIN_WHITE_LIST = var.origin_white_list
      STORAGE_URL       = var.storage_url
    }
  }

  # lifecycle {
  #   ignore_changes = [
  #     last_modified,
  #     source_code_hash,
  #   ]
  # }
}