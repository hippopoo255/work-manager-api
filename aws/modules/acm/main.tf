data "aws_route53_zone" "this" {
  name = local.domain_name
}

/**
 * ========================
 * # ALBと紐付ける
 * - 証明書のリクエスト
 * - CNAMEレコード追加
 * - 検証の待機
 * ========================
 */

# 証明書のリクエスト
resource "aws_acm_certificate" "api" {
  domain_name               = "api.${data.aws_route53_zone.this.name}"
  subject_alternative_names = []
  validation_method         = "DNS"
  provider                  = aws.tokyo

  lifecycle {
    create_before_destroy = true
  }

  tags = {
    Name = local.pj_name_snake
  }
}

# CNAMEレコード追加
resource "aws_route53_record" "api" {
  for_each = {
    for dvo in aws_acm_certificate.api.domain_validation_options : dvo.domain_name => {
      name   = dvo.resource_record_name
      record = dvo.resource_record_value
      type   = dvo.resource_record_type
    }
  }

  zone_id = data.aws_route53_zone.this.zone_id
  name    = each.value.name
  type    = each.value.type
  records = [each.value.record]
  ttl     = 60
}

# 検証の待機
resource "aws_acm_certificate_validation" "api" {
  certificate_arn         = aws_acm_certificate.api.arn
  validation_record_fqdns = [for record in aws_route53_record.api : record.fqdn]
}


/**
 * ========================
 * # cloudfrontと紐付ける
 * - 証明書のリクエスト
 * - CNAMEレコード追加
 * - 検証の待機
 * ========================
 */

# 証明書のリクエスト
resource "aws_acm_certificate" "cdn" {
  domain_name               = "asset.${data.aws_route53_zone.this.name}"
  subject_alternative_names = []
  validation_method         = "DNS"
  # リージョン：バージニア北部
  provider = aws.virginia

  lifecycle {
    create_before_destroy = true
  }

  tags = {
    Name = "${local.pj_name_snake}_cdn"
  }
}

# CNAMEレコード追加(バージニア)
resource "aws_route53_record" "cdn" {
  provider = aws.virginia
  for_each = {
    for dvo in aws_acm_certificate.cdn.domain_validation_options : dvo.domain_name => {
      name   = dvo.resource_record_name
      record = dvo.resource_record_value
      type   = dvo.resource_record_type
    }
  }

  zone_id = data.aws_route53_zone.this.zone_id
  name    = each.value.name
  type    = each.value.type
  records = [each.value.record]
  ttl     = 60
}

# 検証の待機
resource "aws_acm_certificate_validation" "cdn" {
  provider                = aws.virginia
  certificate_arn         = aws_acm_certificate.cdn.arn
  validation_record_fqdns = [for record in aws_route53_record.cdn : record.fqdn]
}
