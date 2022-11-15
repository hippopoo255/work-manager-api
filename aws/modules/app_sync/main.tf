# APIトップ
resource "aws_appsync_graphql_api" "this" {
  authentication_type = "API_KEY"
  name                = local.pj_name_snake

  schema = data.local_file.graphql_schema.content
}

# APIキー
resource "aws_appsync_api_key" "this" {
  api_id  = aws_appsync_graphql_api.this.id
  expires = formatdate("YYYY-MM-DD'T'hh:mm:ssZ", timeadd(timestamp(), "8760h")) # 24 * 365 = 8760
}