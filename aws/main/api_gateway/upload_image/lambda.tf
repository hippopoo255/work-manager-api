data "aws_caller_identity" "self" {}

locals {
  upload_image_arns = [
    "arn:aws:iam::aws:policy/AmazonDynamoDBFullAccess",
    "arn:aws:iam::aws:policy/AmazonS3FullAccess"
  ]
}
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

resource "aws_iam_policy" "lambda_basic_execution" {
  name = "lambda-basic-execution--upload_image"
  policy = templatefile("${path.module}/policy/lambda_execution.json", {
    account_id = data.aws_caller_identity.self.account_id,
  })
}

resource "aws_iam_role" "upload_image" {
  name               = "lambda-role--upload_image"
  assume_role_policy = data.aws_iam_policy_document.lambda_assume_role_policy.json
}

resource "aws_iam_role_policy_attachment" "lambda_basic_execution" {
  role       = aws_iam_role.upload_image.name
  policy_arn = aws_iam_policy.lambda_basic_execution.arn
}

resource "aws_iam_role_policy_attachment" "lambda_other_attachment" {
  for_each = {
    for arn in local.upload_image_arns : arn => arn # key => value
  }
  role       = aws_iam_role.upload_image.name
  policy_arn = each.value
}

# Zip化
data "archive_file" "lambda_function" {
  type        = "zip"
  source_dir  = path.module
  output_path = "${path.module}/lambda_function.zip"
}

resource "aws_lambda_function" "upload_image" {
  filename      = data.archive_file.lambda_function.output_path
  function_name = "upload_image"
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
}