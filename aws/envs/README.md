## Step

### init

```bash:
$ cd init
$ cp main.tfvars.example main.tfvars
# ...customize vars
$ terraform init
$ terraform plan -var-file="main.tfvars"
$ terraform apply -var-file="main.tfvars"
```

### stg / prod

```bash:
$ cd prod
$ cp main.tfvars.example main.tfvars
# ...customize vars
$ terraform init
$ terraform plan -var-file="main.tfvars" -target=module.ssm_parameter.data.template_file.params_structure
$ terraform apply -var-file="main.tfvars" -target=module.ssm_parameter.data.template_file.params_structure
$ terraform plan -var-file="main.tfvars"
$ terraform apply -var-file="main.tfvars"
```

## Optional step

### appsync


### deploy