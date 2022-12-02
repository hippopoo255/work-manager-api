# この後作成するIAMユーザに適用するポリシー
data "aws_iam_policy" "AmazonS3FullAccess" {
  arn = "arn:aws:iam::aws:policy/AmazonS3FullAccess"
}

data "aws_default_tags" "this" {}