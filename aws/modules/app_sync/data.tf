data "aws_caller_identity" "self" {}

# スキーマ
data "local_file" "graphql_schema" {
  filename = "./schema/schema.graphql"
}

data "local_file" "resolvers" {
  for_each = { for f in local.resolver_edge_template_mapping_files : f.name => f }
  filename = "${path.module}/${each.value.path}"
}

data "local_file" "this" {
  for_each = { for f in local.function_edge_template_mapping_files : f.name => f }
  filename = "${path.module}/${each.value.path}"
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

# module.lambdaで作成できた Lambda リソース
data "aws_lambda_function" "this" {
  for_each = { for item in local.functions : item.name => item }

  function_name = each.key
}