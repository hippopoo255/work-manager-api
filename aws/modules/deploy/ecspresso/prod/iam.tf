resource "aws_iam_user" "this" {
  name = "${data.aws_default_tags.this.tags.System}-circleci-${data.aws_default_tags.this.tags.Env}"
}

resource "aws_iam_role" "this" {
  name = "${data.aws_default_tags.this.tags.System}-deploy-${data.aws_default_tags.this.tags.Env}"

  assume_role_policy = jsonencode(
    {
      "Version": "2012-10-17",
      "Statement" : [
        {
          "Effect" : "Allow",
          "Action" : [
            "sts:AssumeRole",
            "sts:TagSession"
          ],
          "Principal" : {
            "AWS" : aws_iam_user.this.arn
          }
        }
      ]
    }
  )
}

# ECRへイメージをプッシュ可能な権限を付与する
resource "aws_iam_role_policy_attachment" "AmazonEC2ContainerRegistryPowerUser" {
  role = aws_iam_role.this.name
  policy_arn = data.aws_iam_policy.AmazonEC2ContainerRegistryPowerUser.arn
}

# resource "aws_iam_role_policy_attachment" "AmazonEC2ContainerRegistryPowerUser" {
#   role = data.aws_iam_role.ecs_task_role.name
#   policy_arn = data.aws_iam_policy.AmazonECSTaskExecutionRolePolicy.arn
# }

# S3で管理しているtfstateを読み取る権限
resource "aws_iam_role_policy" "s3" {
  name = "s3"
  role = aws_iam_role.this.id

  policy = templatefile("${path.module}/role/s3.json", {
    s3_arn = "arn:aws:s3:::${data.aws_default_tags.this.tags.System}-tfstate/${data.aws_default_tags.this.tags.System}/${data.aws_default_tags.this.tags.Env}/deploy/ecspresso/data.tfstate"
  })
}

# ECSのServiceやTaskの更新をする権限
resource "aws_iam_role_policy" "ecs" {
  name = "ecs"
  role = aws_iam_role.this.id

  policy = templatefile("${path.module}/role/ecs.json", {
    ecs_task_arn = data.aws_iam_role.ecs_task_role.arn,
    # ecs_task_execution_arn = data.aws_iam_role.ecs_task_execution.arn,
    ecs_service_arn = data.aws_ecs_service.this.arn
  })
}

# terraform apply後、以下のcliを実行して出力したアクセスキーとシークレットアクセスキーをCircleCIで使う
# aws iam create-access-key --user-name work-manager-circleci-prod
