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
  resolver_edge_template_mapping_files = [
    {
      name            = "req",
      path            = "request_mapping_template/common_resolver.template",
      file_permission = "0755"
    },
    {
      name            = "res",
      path            = "response_mapping_template/common_resolver.template",
      file_permission = "0755"
    },
  ]
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