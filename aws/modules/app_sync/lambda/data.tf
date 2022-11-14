data "aws_caller_identity" "self" {}

# Zip化
data "archive_file" "lambda_functions" {
  for_each = { for i in local.functions : i.name => i }

  type        = "zip"
  source_dir  = "${path.module}/${each.key}"
  output_path = "${path.module}/${each.key}/lambda_function.zip"
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

data "aws_iam_policy" "AmazonDynamoDBFullAccess" {
  arn = "arn:aws:iam::aws:policy/AmazonDynamoDBFullAccess"
}