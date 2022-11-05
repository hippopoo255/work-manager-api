# get_blogs(ds: get_blogs)
# save_blog(ds: save_blog)
# ...


resource "aws_appsync_function" "this" {
  for_each = { for item in local.functions : item.name => item }

  api_id      = aws_appsync_graphql_api.this.id
  data_source = aws_appsync_datasource.this[each.key].name
  name        = each.key

  request_mapping_template = <<EOF
{
  "operation": "Invoke",
  "payload": $util.toJson($context)
}
EOF

  response_mapping_template = <<EOF
#if($ctx.error)
  $util.error($ctx.error.message, $ctx.error.type)
#end
  $util.toJson($context.result)
EOF


}
