provider "aws" {
  region = "ap-northeast-1"
}

data "aws_route53_zone" "default" {
  name = var.domain_name
}

resource "aws_s3_bucket" "public" {
  bucket = format("asset.%s", data.aws_route53_zone.default.name)
  acl    = "public-read"
  policy = templatefile("${path.module}/policies/s3_policy.json", {
    domain_name = var.domain_name
  })
  website {
    index_document = "index.html"
    error_document = "error.html"
  }
  cors_rule {
    allowed_headers = ["*"]
    allowed_methods = ["PUT", "POST", "DELETE", "GET"]
    # allowed_origins = ["https://${format("api.%s", data.aws_route53_zone.default.name)}"]
    allowed_origins = ["*"]
    max_age_seconds = 6000
  }
}

# TODO: S3にassetsディレクトリをアップロード