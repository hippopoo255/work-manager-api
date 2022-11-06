# Query
# blogs(function1: get_blogs)
# blog(function1:  get_blog_by_id)
# tags(function1:  get_blogs)

# Mutation
# createBlog(function1: save_blog)
# updateBlog(function1: save_blog)
# deleteBlog(function1: delete_blog)

resource "aws_appsync_resolver" "this" {
  for_each = { for item in local.resolvers : item.field => item }

  api_id = aws_appsync_graphql_api.this.id
  field  = each.value.field
  type   = each.value.type
  kind   = "PIPELINE"

  request_template  = "{}"
  response_template = "$util.toJson($ctx.result)"

  pipeline_config {
    functions = [
      aws_appsync_function.this[each.value.functions].function_id,
    ]
  }
}