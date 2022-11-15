resource "aws_iam_policy" "lambda_basic_executions" {
  for_each = { for i in local.functions : i.name => i }

  name = "lambda-basic-execution--${each.value.name}"
  policy = templatefile("${path.module}/role/lambda_execution.json", {
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
