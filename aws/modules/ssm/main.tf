module "iam_user" {
  source = "../iam_user"
}

resource "null_resource" "list_access_key" {

  provisioner "local-exec" {
    command = "${path.module}/list_access_keys.sh"

    environment = {
      AWS_IAM_USER_NAME = module.iam_user.user_name
      PATH_MODULE = path.module
    }
  }
}

resource "null_resource" "create_access_key" {

  provisioner "local-exec" {
    command = length(jsondecode(data.template_file.list_access_keys.rendered).AccessKeyMetadata) != 2 ? "${path.module}/create_access_key.sh" : "${path.module}/update_access_key.sh"

    environment = {
      AWS_IAM_USER_NAME = module.iam_user.user_name
      PATH_MODULE = path.module
      COUNT = length(jsondecode(data.template_file.list_access_keys.rendered).AccessKeyMetadata)
    }
  }

  depends_on = [null_resource.list_access_key]
}

resource "aws_ssm_parameter" "this" {
  for_each  = { for k, v in local.param_list : v.Name => v }
  value     = each.value.Value
  name      = each.value.Name
  type      = each.value.Type
  overwrite = true

  depends_on = [data.template_file.params_structure]
}

resource "null_resource" "delete_credential_file" {

  provisioner "local-exec" {
    command = "${path.module}/delete_json.sh"

    environment = {
      PATH_MODULE = path.module
    }
  }

  depends_on = [data.template_file.params_structure]
}