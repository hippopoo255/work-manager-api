# Query
# blogs(function1: get_blogs)
# blog(function1:  get_blog_by_id)
# tags(function1:  get_blogs)

# Mutation
# createBlog(function1: save_blog)
# updateBlog(function1: save_blog)
# deleteBlog(function1: delete_blog)
locals {
  resolver_edge_template_mapping_files = [
    {
      name            = "req",
      path            = "request_mapping_template/common_resolver.template",
      file_permission = "0777"
    },
    {
      name            = "res",
      path            = "response_mapping_template/common_resolver.template",
      file_permission = "0777"
    },
  ]
}

data "local_file" "resolvers" {
  for_each = { for f in local.resolver_edge_template_mapping_files : f.name => f }
  filename = "${path.module}/${each.value.path}"
}

resource "aws_appsync_resolver" "this" {
  for_each = { for item in local.resolvers : item.field => item }

  api_id = aws_appsync_graphql_api.this.id
  field  = each.value.field
  type   = each.value.type
  kind   = "PIPELINE"

  request_template  = data.local_file.resolvers["req"].content
  response_template = data.local_file.resolvers["res"].content

  pipeline_config {
    functions = [
      aws_appsync_function.this[each.value.functions].function_id,
    ]
  }
}