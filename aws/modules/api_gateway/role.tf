resource "aws_iam_role" "push_cloudwatch" {
  name = "apigateway-push-cloudwatch-role-${data.aws_default_tags.this.tags.Env}"
  assume_role_policy = data.aws_iam_policy_document.api_gateway_assume_role_policy.json
}

resource "aws_iam_role_policy_attachment" "lambda_basic_execution" {
  role       = aws_iam_role.push_cloudwatch.name
  policy_arn = data.aws_iam_policy.AmazonAPIGatewayPushToCloudWatchLogs.arn
}