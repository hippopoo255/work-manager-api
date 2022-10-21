variable "name_prefix" {
  type    = string
  default = "pj_name_snake"
}

data "aws_iam_policy_document" "admin" {
  statement {
    effect = "Allow"

    actions   = ["*"]
    resources = ["*"]

    principals {
      type        = "Service"
      identifiers = ["ec2.amazonaws.com"]
    }
  }
}

resource "aws_iam_policy" "admin" {
  name   = "${var.name_prefix}_admin"
  policy = file("./iam_role/administrator.json")
}

resource "aws_iam_role" "admin" {
  name               = "${var.name_prefix}_admin"
  assume_role_policy = file("./iam_role/administrator.json")
}

resource "aws_iam_role_policy_attachment" "admin" {
  role       = aws_iam_role.admin.name
  policy_arn = aws_iam_policy.admin.arn
}

output "iam_role_arn" {
  value = aws_iam_role.admin.arn
}

output "iam_role_name" {
  value = aws_iam_role.admin.name
}
