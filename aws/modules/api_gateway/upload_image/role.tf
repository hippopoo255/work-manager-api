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