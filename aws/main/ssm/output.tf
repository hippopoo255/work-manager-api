output "json_body" {
  value = data.template_file.params_structure.rendered
}