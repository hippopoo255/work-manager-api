resource "aws_ssm_parameter" "this" {
  count     = length(local.param_list)
  value     = local.param_list[count.index].Value
  name      = local.param_list[count.index].Name
  type      = local.param_list[count.index].Type
  overwrite = true

  depends_on = [data.template_file.params_structure]
}