# ロールの信頼ポリシー
data "aws_iam_policy_document" "ecs_assume_role" {
  source_json = file("${path.module}/policy/ecs_policy_principal.json")
}

data "aws_iam_policy_document" "ec2_assume_role" {
  source_json = file("${path.module}/policy/ec2_policy_principal.json")
}


# ロールの作成
/*
  適用するポリシーのない空っぽのロール
  今回は後述のaws_iam_role_policy_attachmentでポリシーを適用する
  本リソースにmanaged_policy_arns = [...] を追記して、直接適用することもできるがその場合、
  Terraform上で管理していないポリシーをmy-ecs-roleに(コンソール画面などから)アタッチしている場合、
  そのポリシーはデタッチされる
*/
resource "aws_iam_role" "ecs_task_role" {
  name               = "ecs-task-role-${data.aws_default_tags.this.tags.Env}"
  assume_role_policy = data.aws_iam_policy_document.ecs_assume_role.json
}

resource "aws_iam_role" "ecs_instance_role" {
  name               = "ecs-instance-role-${data.aws_default_tags.this.tags.Env}"
  assume_role_policy = data.aws_iam_policy_document.ec2_assume_role.json
}

# ポリシーの作成
resource "aws_iam_policy" "ecs_env_connection" {
  name = "ecs-env-connection-${data.aws_default_tags.this.tags.Env}"
  # SSMのパラメータストアにアクセス、KMSによる復号等の権限
  policy = file("${path.module}/policy/ecs_env_connection.json")
}

# 各ロールにポリシーをアタッチ
resource "aws_iam_role_policy_attachment" "my_ecs_env_connect_attachment" {
  role       = aws_iam_role.ecs_task_role.name
  policy_arn = aws_iam_policy.ecs_env_connection.arn
}

resource "aws_iam_role_policy_attachment" "my_ecs_other_attachment" {
  for_each = {
    for arn in local.ecs_policy_arns : arn => arn # key => value
  }
  role       = aws_iam_role.ecs_task_role.name
  policy_arn = each.value
}

resource "aws_iam_role_policy_attachment" "ecs_instance_other_attachment" {
  for_each = {
    for arn in local.ec2_policy_arns : arn => arn
  }
  role       = aws_iam_role.ecs_instance_role.name
  policy_arn = each.value
}

resource "aws_iam_instance_profile" "ecs_instance_profile" {
  name = "ecs-instance-profile-${data.aws_default_tags.this.tags.Env}"
  role = aws_iam_role.ecs_instance_role.name
}