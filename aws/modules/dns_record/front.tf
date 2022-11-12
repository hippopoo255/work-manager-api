data "aws_route53_zone" "this" {
  name = local.domain_name
}

# CNAMEレコード追加
resource "aws_route53_record" "app" {
  for_each = {
    for record in var.front_hosting_settings.app.records : record.type => record
  }

  zone_id = data.aws_route53_zone.this.zone_id
  name    = "${each.value.server_name}${data.aws_route53_zone.this.name}"
  type    = each.value.type
  records = [each.value.value]
  ttl     = 300
}

resource "aws_route53_record" "admin" {
  for_each = {
    for record in var.front_hosting_settings.admin.records : record.type => record
  }

  zone_id = data.aws_route53_zone.this.zone_id
  name    = "${each.value.server_name}${data.aws_route53_zone.this.name}"
  type    = each.value.type
  records = [each.value.value]
  ttl     = 300
}