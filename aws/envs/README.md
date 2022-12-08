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
$ cp ../modules/ssm/ssm.example.json ../modules/ssm/ssm.json
# ...customize params on modules/ssm/ssm.json

$ cd prod
$ cp main.tfvars.example main.tfvars
# ...customize vars on main.tfvars

$ terraform init
$ terraform plan -var-file="main.tfvars" -target=module.ssm_parameter.data.template_file.params_structure
$ terraform apply -var-file="main.tfvars" -target=module.ssm_parameter.data.template_file.params_structure
$ terraform plan -var-file="main.tfvars"
$ terraform apply -var-file="main.tfvars"
```

## Optional step

### appsync


### deploy