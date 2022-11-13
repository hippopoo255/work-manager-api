# get_blogs(ds: get_blogs)
# save_blog(ds: save_blog)
# ...

locals {
  function_edge_template_mapping_files = [
    {
      name = "req",
      path = "request_mapping_template/common_function.template"
    },
    {
      name = "res",
      path = "response_mapping_template/common_function.template"
    },
  ]
}

data "local_file" "this" {
  for_each = { for f in local.function_edge_template_mapping_files : f.name => f }
  filename = "${path.module}/${each.value.path}"
}

resource "aws_appsync_function" "this" {
  for_each = { for item in local.functions : item.name => item }

  api_id      = aws_appsync_graphql_api.this.id
  data_source = aws_appsync_datasource.this[each.key].name
  name        = each.key

  request_mapping_template  = data.local_file.this["req"].content
  response_mapping_template = data.local_file.this["res"].content
}
