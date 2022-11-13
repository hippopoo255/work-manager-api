# 他のリソースからS3への書き込み等アクセスを行うための汎用的なIAMユーザ

# この後作成するIAMユーザに適用するポリシー
data "aws_iam_policy" "AmazonS3FullAccess" {
  arn = "arn:aws:iam::aws:policy/AmazonS3FullAccess"
}

# IAMユーザの作成
resource "aws_iam_user" "s3" {
  name = "s3-full-access-user"
  path = "/"
}

# 作成したIAMユーザにポリシーを適用
resource "aws_iam_user_policy_attachment" "s3" {
  user       = aws_iam_user.s3.name
  policy_arn = data.aws_iam_policy.AmazonS3FullAccess.arn
}