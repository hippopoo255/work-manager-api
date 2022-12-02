# 他のリソースからS3への書き込み等アクセスを行うための汎用的なIAMユーザ

# IAMユーザの作成
resource "aws_iam_user" "s3" {
  name = "s3-full-access-user-${data.aws_default_tags.this.tags.Env}"
  path = "/"
}

# 作成したIAMユーザにポリシーを適用
resource "aws_iam_user_policy_attachment" "s3" {
  user       = aws_iam_user.s3.name
  policy_arn = data.aws_iam_policy.AmazonS3FullAccess.arn
}