locals {
  param_list = jsondecode(data.template_file.params_structure.rendered).Parameters
}