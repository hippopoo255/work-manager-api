# resolver

locals {
  functions = [
    { name = "delete_blog", field = "deleteBlog", type = "Mutation" },
    { name = "get_blog_by_id", field = "blog", type = "Query" },
    { name = "get_blogs", field = "blogs", type = "Query" },
    { name = "get_tags", field = "tags", type = "Query" },
    { name = "save_blog", field = "updateBlog", type = "Mutation" },
    # { name = "upload_image", env="" },
  ]
  resolvers = [
    { functions = "delete_blog", field = "deleteBlog", type = "Mutation" },
    { functions = "get_blog_by_id", field = "blog", type = "Query" },
    { functions = "get_blogs", field = "blogs", type = "Query" },
    { functions = "get_tags", field = "tags", type = "Query" },
    { functions = "save_blog", field = "updateBlog", type = "Mutation" },
    { functions = "save_blog", field = "createBlog", type = "Mutation" },
  ]
}

# スキーマ
data "local_file" "graphql_schema" {
  filename = "./schema/schema.graphql"
}

# APIトップ
resource "aws_appsync_graphql_api" "this" {
  authentication_type = "API_KEY"
  name                = var.pj_name_snake

  schema = data.local_file.graphql_schema.content
}

# APIキー
resource "aws_appsync_api_key" "this" {
  api_id  = aws_appsync_graphql_api.this.id
  expires = formatdate("YYYY-MM-DD'T'hh:mm:ssZ", timeadd(timestamp(), "8760h")) # 24 * 365 = 8760
}